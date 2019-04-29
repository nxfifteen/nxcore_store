<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 26/04/2019
     * Time: 22:47
     */

    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BodyBmi;
    use App\Entity\BodyFat;
    use App\Entity\BodyWeight;
    use App\Entity\CaffeineIntake;
    use App\Entity\CountDailyFloor;
    use App\Entity\Exercise;
    use App\Entity\Patient;
    use App\Entity\ThirdPartyService;
    use App\Entity\TrackingDevice;
    use App\Entity\UnitOfMeasurement;
    use App\Entity\WaterIntake;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
    use App\Entity\ApiAccessLog;
    use App\Entity\CountDailyStep;
    use DateInterval;
    use DateTime;
    use DateTimeInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use phpDocumentor\Reflection\Types\Mixed_;
    use PhpParser\Node\Expr\Cast\Object_;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class PreWriteSubscriber implements EventSubscriberInterface
    {
        private $em;
        private $logManager;

        public function __construct( EntityManagerInterface $em, SiteLogManager $logManager )
        {
            $this->em = $em;
            $this->logManager = $logManager;
        }

        /**
         * Returns an array of event names this subscriber wants to listen to.
         *
         * The array keys are event names and the value can be:
         *
         *  * The method name to call (priority defaults to 0)
         *  * An array composed of the method name to call and the priority
         *  * An array of arrays composed of the method names to call and respective
         *    priorities, or 0 if unset
         *
         * For instance:
         *
         *  * ['eventName' => 'methodName']
         *  * ['eventName' => ['methodName', $priority]]
         *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => [ 'modifyWrite', EventPriorities::PRE_WRITE ],
            ];
        }

        /**
         * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
         * @return mixed
         */
        public function modifyWrite( GetResponseForControllerResultEvent $event )
        {
//            $this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
            $trackedEntity = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            if ( Request::METHOD_POST !== $method ) {
                return $trackedEntity;
            }

            $new = FALSE;

            $entityClass = str_ireplace("App\\Entity\\", "", get_class($trackedEntity));
            $modifyWriteMethod = "modifyWrite" . $entityClass;
            if ( !$trackedEntity instanceof CountDailyStep &&
                !$trackedEntity instanceof BodyWeight &&
                !$trackedEntity instanceof BodyBmi &&
                !$trackedEntity instanceof BodyFat &&
                !$trackedEntity instanceof CountDailyFloor &&
                !$trackedEntity instanceof WaterIntake &&
                !$trackedEntity instanceof CaffeineIntake &&
                !$trackedEntity instanceof Exercise) {
                $this->logManager->nxrInfo("Unknown write event from " . $entityClass);

                if (!method_exists($this, $modifyWriteMethod)) {
                    $this->logManager->nxrInfo("No modify write method = " . $modifyWriteMethod);
                }

            } else if (!method_exists($this, $modifyWriteMethod)) {
                $this->logManager->nxrInfo("No modify write method = " . $modifyWriteMethod);
            } else {
                if ( !method_exists($trackedEntity, "getDateTime") ) {
                    $this->logManager->nxrInfo("Write event from " . $entityClass);
                } else {
                    $this->logManager->nxrInfo("Write event from " . $entityClass . " - " . $trackedEntity->getDateTime()->format("Y-m-d H:i:s"));
                }

                list($new, $trackedEntity) = $this->$modifyWriteMethod($trackedEntity, $event->getRequest());

                if ( method_exists($trackedEntity, "getThirdPartyService") ) {
                    $thirdPartyService = $trackedEntity->getThirdPartyService();
                } else {
                    $thirdPartyService = NULL;
                }
                $this->updateLastApiAccessLog($entityClass, $trackedEntity->getPatient(), $thirdPartyService, $trackedEntity->getDateTime());
            }
//            $this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

            if (!$new) {
                $event->setControllerResult(NULL);
            }

            return $trackedEntity;
        }

        public function modifyWriteCountDailyStep( CountDailyStep $trackedEntity, Request $request ) {
            /** @var CountDailyStep[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $thirdPartyService = $trackedEntity->getThirdPartyService();
            if (!$trackedEntity->getTrackingDevice() instanceof TrackingDevice) {
                $trackedEntity->setTrackingDevice($this->getTrackerEntry($trackedEntity, $request));
            }

            $previousData = $this->em->getRepository(CountDailyStep::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }


            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getValue() != $trackedEntity->getValue() ) {
                $dataChanged = true;
                $previousData->setValue($trackedEntity->getValue());
            }

            if ( $previousData->getTrackingDevice() !== $trackedEntity->getTrackingDevice() ) {
                $dataChanged = true;
                $previousData->setTrackingDevice($trackedEntity->getTrackingDevice());
            }

            if ( $previousData->getGoal() != $trackedEntity->getGoal() ) {
                $dataChanged = true;
                if (is_numeric($trackedEntity->getGoal())) {
                    $previousData->setGoal($trackedEntity->getGoal());
                } else {
                    $previousData->setGoal($trackerPatient->getStepGoal());
                }
            } else if (!is_numeric($previousData->getGoal())) {
                $dataChanged = true;
                $previousData->setGoal($trackerPatient->getStepGoal());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteCountDailyFloor( CountDailyFloor $trackedEntity, Request $request ) {
            /** @var CountDailyFloor[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $thirdPartyService = $trackedEntity->getThirdPartyService();
            if (!$trackedEntity->getTrackingDevice() instanceof TrackingDevice) {
                $trackedEntity->setTrackingDevice($this->getTrackerEntry($trackedEntity, $request));
            }

            $previousData = $this->em->getRepository(CountDailyFloor::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getValue() != $trackedEntity->getValue() ) {
                $dataChanged = true;
                $previousData->setValue($trackedEntity->getValue());
            }

            if ( $previousData->getTrackingDevice() !== $trackedEntity->getTrackingDevice() ) {
                $dataChanged = true;
                $previousData->setTrackingDevice($trackedEntity->getTrackingDevice());
            }

            if ( $previousData->getGoal() != $trackedEntity->getGoal() ) {
                $dataChanged = true;
                if (is_numeric($trackedEntity->getGoal())) {
                    $previousData->setGoal($trackedEntity->getGoal());
                } else {
                    $previousData->setGoal($trackerPatient->getFloorGoal());
                }
            } else if (!is_numeric($previousData->getGoal())) {
                $dataChanged = true;
                $previousData->setGoal($trackerPatient->getFloorGoal());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteBodyWeight( BodyWeight $trackedEntity, Request $request ) {
            /** @var BodyWeight[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $thirdPartyService = $trackedEntity->getThirdPartyService();

            $previousData = $this->em->getRepository(BodyWeight::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getGoal() != $trackedEntity->getGoal() ) {
                $dataChanged = true;
                $previousData->setGoal($trackedEntity->getGoal());
            }

            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
                $dataChanged = true;
                $previousData->setPartOfDay($trackedEntity->getPartOfDay());
                $previousData->setMeasurement($trackedEntity->getMeasurement());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteBodyBmi( BodyBmi $trackedEntity, Request $request ) {
            /** @var BodyBmi[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();

            $previousData = $this->em->getRepository(BodyBmi::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
                $dataChanged = true;
                $previousData->setMeasurement($trackedEntity->getMeasurement());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteBodyFat( BodyFat $trackedEntity, Request $request ) {
            /** @var BodyFat[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $thirdPartyService = $trackedEntity->getThirdPartyService();

            $previousData = $this->em->getRepository(BodyFat::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getGoal() != $trackedEntity->getGoal() ) {
                $dataChanged = true;
                $previousData->setGoal($trackedEntity->getGoal());
            }

            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
                $dataChanged = true;
                $previousData->setPartOfDay($trackedEntity->getPartOfDay());
                $previousData->setMeasurement($trackedEntity->getMeasurement());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteWaterIntake( WaterIntake $trackedEntity, Request $request ) {
            /** @var WaterIntake[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $trackingDevice = $trackedEntity->getTrackingDevice();
            if (!$trackingDevice instanceof TrackingDevice) {
                $trackingDevice = $this->getTrackerEntry($trackedEntity, $request);
                $trackedEntity->setTrackingDevice($trackingDevice);
            }

            if (!$trackedEntity->getUnitOfMeasurement() instanceof UnitOfMeasurement) {
                $trackedEntity->setUnitOfMeasurement($this->getUnitOfMeasurementEntry($trackedEntity, $request));
            }

//            $this->logManager->nxrInfo(__FILE__ . '@' . __LINE__ . ' = ' . $trackedEntity->getMeasurement());

            $previousData = $this->em->getRepository(WaterIntake::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'trackingDevice' => $trackingDevice ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
                $dataChanged = true;
                $previousData->setMeasurement($trackedEntity->getMeasurement());
            }

            if ( $previousData->getComment() != $trackedEntity->getComment() ) {
                $dataChanged = true;
                $previousData->setComment($trackedEntity->getComment());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteCaffeineIntake( CaffeineIntake $trackedEntity, Request $request ) {
            /** @var CaffeineIntake[] $previousData */
            $trackerDate = $trackedEntity->getDateTime();
            $trackerPatient = $trackedEntity->getPatient();
            $trackingDevice = $trackedEntity->getTrackingDevice();
            if (!$trackingDevice instanceof TrackingDevice) {
                $trackingDevice = $this->getTrackerEntry($trackedEntity, $request);
                $trackedEntity->setTrackingDevice($trackingDevice);
            }

            $previousData = $this->em->getRepository(CaffeineIntake::class)
                ->findBy([ 'date_time' => $trackerDate, 'patient' => $trackerPatient, 'trackingDevice' => $trackingDevice ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
            $previousData = $previousData[0];

            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
                $dataChanged = true;
                $previousData->setMeasurement($trackedEntity->getMeasurement());
            }

            if ( $previousData->getComment() != $trackedEntity->getComment() ) {
                $dataChanged = true;
                $previousData->setComment($trackedEntity->getComment());
            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        public function modifyWriteExercise( Exercise $trackedEntity, Request $request ) {
            /** @var Exercise[] $previousData */
            $trackerStartDate = $trackedEntity->getDateTime();
            $trackerEndDate = $trackedEntity->getDateTimeEnd();
            $trackerPatient = $trackedEntity->getPatient();
            $trackingDevice = $trackedEntity->getTracker();
            if (!$trackingDevice instanceof TrackingDevice) {
                $trackingDevice = $this->getTrackerEntry($trackedEntity, $request);
                $trackedEntity->setTracker($trackingDevice);
            }

            $trackedEntity->setDuration($trackerEndDate->format("U") - $trackerStartDate->format("U"));

            $previousData = $this->em->getRepository(Exercise::class)
                ->findBy([ 'date_time' => $trackerStartDate, 'date_time_end' => $trackerEndDate, 'patient' => $trackerPatient, 'tracker' => $trackingDevice ]);

            if (!$previousData) {
                return [TRUE, $trackedEntity];
            }

            $dataChanged = false;
//            $previousData = $previousData[0];
//
//            if ( $previousData->getMeasurement() != $trackedEntity->getMeasurement() ) {
//                $dataChanged = true;
//                $previousData->setMeasurement($trackedEntity->getMeasurement());
//            }

            if($dataChanged) {
                $this->em->flush();
            }

            return [FALSE, $trackedEntity];
        }

        private function updateLastApiAccessLog( String $entityClass, Patient $getPatient, $getThirdPartyService, DateTimeInterface $getDateTime )
        {
            /** @var ApiAccessLog[] $previousData */
            if (!is_null($getThirdPartyService)) {
                $previousData = $this->em->getRepository(ApiAccessLog::class)
                    ->findBy([ 'entity'            => $entityClass, 'patient' => $getPatient->getId(),
                               'thirdPartyService' => $getThirdPartyService->getId()
                    ]);
            } else {
                $previousData = $this->em->getRepository(ApiAccessLog::class)
                    ->findBy([ 'entity'  => $entityClass, 'patient' => $getPatient->getId() ]);
            }

            if (!$previousData) {
                $this->logManager->nxrInfo(__LINE__);

                $lastAccessLog = new ApiAccessLog();

                $lastAccessLog->setPatient($getPatient);
                if (!is_null($getThirdPartyService)) $lastAccessLog->setThirdPartyService($getThirdPartyService);
                $lastAccessLog->setEntity($entityClass);
                $lastAccessLog->setLastRetrieved($getDateTime);
                $lastAccessLog->setLastPulled(new DateTime());
                try {
                    $lastAccessLog->setCooldown(( new DateTime() )->add(new DateInterval('PT10M')));
                } catch ( \Exception $e ) {
                    $lastAccessLog->setCooldown( new DateTime() );
                }

                $this->em->persist($lastAccessLog);
                $this->em->flush();
            } else {
                $lastAccessLog = $previousData[0];

                if (strtotime($lastAccessLog->getLastRetrieved()->format("Y-m-d H:i:s")) < strtotime($getDateTime->format("Y-m-d H:i:s"))) {
                    $lastAccessLog->setLastRetrieved($getDateTime);
                } else {
                    $lastAccessLog->setLastRetrieved($lastAccessLog->getLastRetrieved());
                }
                $lastAccessLog->setLastPulled(new DateTime());
                try {
                    $lastAccessLog->setCooldown(( new DateTime() )->add(new DateInterval('PT10M')));
                } catch ( \Exception $e ) {
                    $lastAccessLog->setCooldown( new DateTime() );
                }

                $this->em->persist($lastAccessLog);
                $this->em->flush();
            }
        }

        private function getTrackerEntry( $trackedEntity, Request $request) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            $postContents = json_decode($request->getContent(), TRUE);
            if ( array_key_exists("x-trackingDevice", $postContents) ) {
                /** @var TrackingDevice $deviceEntity */
                $deviceEntity = $this->em->getRepository(TrackingDevice::class)
                    ->findOneByRemoteId($postContents[ 'x-trackingDevice' ]);

                if ( !$deviceEntity ) {
                    $deviceEntity = new TrackingDevice();
                    $deviceEntity->setPatient($trackedEntity->getPatient());
                    $deviceEntity->setName("");
                    $deviceEntity->setBattery(0);
                    $deviceEntity->setLastSyncTime($trackedEntity->getDateTime());
                    $deviceEntity->setRemoteId($postContents[ 'x-trackingDevice' ]);
                    $deviceEntity->setType('TRACKER');
                    $deviceEntity->setService($trackedEntity->getThirdPartyService());
                } else {
                    if ( $trackedEntity->getDateTime()->format("U") > $deviceEntity->getLastSyncTime()->format("U") ) {
                        $deviceEntity->setLastSyncTime($trackedEntity->getDateTime());
                    }
                }
                $this->em->persist($deviceEntity);

                return $deviceEntity;
            }
        }

        private function getUnitOfMeasurementEntry( $trackedEntity, Request $request) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            $postContents = json_decode($request->getContent(), TRUE);
            if ( array_key_exists("x-unitOfMeasurement", $postContents) ) {
                /** @var UnitOfMeasurement $deviceEntity */
                $deviceEntity = $this->em->getRepository(UnitOfMeasurement::class)
                    ->findOneByName($postContents[ 'x-unitOfMeasurement' ]);

                if ( !$deviceEntity ) {
                    $deviceEntity = new UnitOfMeasurement();
                    $deviceEntity->setName($postContents[ 'x-unitOfMeasurement' ]);
                }

                $this->em->persist($deviceEntity);

                return $deviceEntity;
            }
        }
    }