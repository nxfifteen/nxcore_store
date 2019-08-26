<?php

/*
 * This file is part of the Storage module in NxFIFTEEN Core.
 *
 * Copyright (c) 2019. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Store
 * @version     0.0.0.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://git.nxfifteen.rocks/nx-health NxFIFTEEN Core
 * @link        https://git.nxfifteen.rocks/nx-health/store NxFIFTEEN Core Storage
 * @copyright   2019 Stuart McCulloch Anderson
 * @license     https://license.nxfifteen.rocks/mit/2015-2019/ MIT
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\ApiAccessLog;
use App\Entity\BodyBmi;
use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\CaffeineIntake;
use App\Entity\CountDailyCalories;
use App\Entity\CountDailyDistance;
use App\Entity\CountDailyFloor;
use App\Entity\CountDailyStep;
use App\Entity\Exercise;
use App\Entity\IntradayStep;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Entity\WaterIntake;
use App\Logger\SiteLogManager;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PreWriteSubscriber implements EventSubscriberInterface
{
    private $em;
    private $logManager;

    public function __construct(EntityManagerInterface $em, SiteLogManager $logManager)
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
            KernelEvents::VIEW => ['modifyWrite', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @return mixed
     */
    public function modifyWrite(GetResponseForControllerResultEvent $event)
    {
//            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        $trackedEntity = $event->getControllerResult();
        //$this->logManager->nxrInfo(print_r($event->getControllerResult(), true));
        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method) {
            return $trackedEntity;
        }

        $new = FALSE;

        $entityClass = str_ireplace("App\\Entity\\", "", get_class($trackedEntity));
        $modifyWriteMethod = "modifyWrite" . $entityClass;
        if (!$trackedEntity instanceof CountDailyStep &&
            !$trackedEntity instanceof BodyWeight &&
            !$trackedEntity instanceof BodyBmi &&
            !$trackedEntity instanceof BodyFat &&
            !$trackedEntity instanceof CountDailyFloor &&
            !$trackedEntity instanceof WaterIntake &&
            !$trackedEntity instanceof CaffeineIntake &&
            !$trackedEntity instanceof Exercise &&
            !$trackedEntity instanceof IntradayStep &&
            !$trackedEntity instanceof TrackingDevice &&
            !$trackedEntity instanceof CountDailyDistance &&
            !$trackedEntity instanceof CountDailyCalories) {
            $this->logManager->nxrInfo("Unknown write event from " . $entityClass);

            if (!method_exists($this, $modifyWriteMethod)) {
                $this->logManager->nxrInfo("No modify write method = " . $modifyWriteMethod);
            }

        } else if (!method_exists($this, $modifyWriteMethod)) {
            $this->logManager->nxrInfo("No modify write method = " . $modifyWriteMethod);
        } else {
            if (method_exists($trackedEntity, "getDateTime")) {
                $this->logManager->nxrInfo("Write event from " . $entityClass . " - " . $trackedEntity->getDateTime()->format("Y-m-d H:i:s"));
            } else if (method_exists($trackedEntity, "getDate")) {
                $this->logManager->nxrInfo("Write event from " . $entityClass . " - " . $trackedEntity->getDate()->format("Y-m-d H:i:s"));
            } else {
                $this->logManager->nxrInfo("Write event from " . $entityClass);
            }
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
            list($new, $trackedEntity) = $this->$modifyWriteMethod($trackedEntity, $event->getRequest());
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

            if (method_exists($trackedEntity, "getThirdPartyService")) {
                //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
                $thirdPartyService = $trackedEntity->getThirdPartyService();
            } else {
                //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
                $thirdPartyService = NULL;
            }
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
            if (method_exists($trackedEntity, "getDateTime")) {
                $this->updateLastApiAccessLog($entityClass, $trackedEntity->getPatient(), $thirdPartyService, $trackedEntity->getDateTime());
            } else if (method_exists($trackedEntity, "getDate")) {
                $this->updateLastApiAccessLog($entityClass, $trackedEntity->getPatient(), $thirdPartyService, $trackedEntity->getDate());
            }
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        }
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

        if (!$new) {
            $event->setControllerResult(NULL);
        }

        return $trackedEntity;
    }

    private function updateLastApiAccessLog(String $entityClass, Patient $getPatient, $getThirdPartyService, DateTimeInterface $getDateTime)
    {
        /** @var ApiAccessLog[] $previousData */
        if (!is_null($getThirdPartyService)) {
            $previousData = $this->em->getRepository(ApiAccessLog::class)
                ->findBy(['entity' => $entityClass, 'patient' => $getPatient->getId(),
                    'thirdPartyService' => $getThirdPartyService->getId()
                ]);
        } else {
            $previousData = $this->em->getRepository(ApiAccessLog::class)
                ->findBy(['entity' => $entityClass, 'patient' => $getPatient->getId()]);
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
                $lastAccessLog->setCooldown((new DateTime())->add(new DateInterval('PT10M')));
            } catch (\Exception $e) {
                $lastAccessLog->setCooldown(new DateTime());
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
                $lastAccessLog->setCooldown((new DateTime())->add(new DateInterval('PT10M')));
            } catch (\Exception $e) {
                $lastAccessLog->setCooldown(new DateTime());
            }

            $this->em->persist($lastAccessLog);
            $this->em->flush();
        }
    }

    public function modifyWriteCountDailyStep(CountDailyStep $trackedEntity, Request $request)
    {
        /** @var CountDailyStep[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();
        if (!$trackedEntity->getTrackingDevice() instanceof TrackingDevice) {
            $trackedEntity->setTrackingDevice($this->getTrackerEntry($trackedEntity, $request));
        }

        $previousData = $this->em->getRepository(CountDailyStep::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService, 'trackingDevice' => $trackedEntity->getTrackingDevice()]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }


        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getValue() != $trackedEntity->getValue()) {
            $dataChanged = true;
            $previousData->setValue($trackedEntity->getValue());
        }

        if ($previousData->getTrackingDevice() !== $trackedEntity->getTrackingDevice()) {
            $dataChanged = true;
            $previousData->setTrackingDevice($trackedEntity->getTrackingDevice());
        }

        if (!is_numeric($previousData->getGoal()) && !is_numeric($trackedEntity->getGoal())) {
            $dataChanged = true;
            $previousData->setGoal($trackerPatient->getStepGoal());
        } else if ($previousData->getGoal() != $trackedEntity->getGoal()) {
            $dataChanged = true;
            if (is_numeric($trackedEntity->getGoal())) {
                $previousData->setGoal($trackedEntity->getGoal());
            } else {
                $previousData->setGoal($trackerPatient->getStepGoal());
            }
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    private function getTrackerEntry($trackedEntity, Request $request)
    {
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        /** @noinspection PhpComposerExtensionStubsInspection */
        $postContents = json_decode($request->getContent(), TRUE);
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        if (array_key_exists("x-trackingDevice", $postContents)) {
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
            /** @var TrackingDevice $deviceEntity */
            $deviceEntity = $this->em->getRepository(TrackingDevice::class)
                ->findOneByRemoteId($postContents['x-trackingDevice']);
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

            /** @var DateTime $lastDated */
            if (method_exists($trackedEntity, "getDateTime" ) && $trackedEntity->getDateTime()) {
                $lastDated = $trackedEntity->getDateTime();
            } else if (method_exists($trackedEntity, "getDate" ) && $trackedEntity->getDate()) {
                $lastDated = $trackedEntity->getDate();
            } else {
                $lastDated = new DateTime("Y-m-d");
            }

            if (!$deviceEntity) {
                $this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
                $deviceEntity = new TrackingDevice();
                $deviceEntity->setPatient($trackedEntity->getPatient());
                $deviceEntity->setName("");
                $deviceEntity->setBattery(null);
                $deviceEntity->setLastSyncTime($lastDated);
                $deviceEntity->setRemoteId($postContents['x-trackingDevice']);
                $deviceEntity->setType('TRACKER');
                $deviceEntity->setService($trackedEntity->getThirdPartyService());
            } else {
                if (is_null($deviceEntity->getLastSyncTime()) || $lastDated->format("U") > $deviceEntity->getLastSyncTime()->format("U")) {
                    $deviceEntity->setLastSyncTime($lastDated);
                }
            }
            $this->em->persist($deviceEntity);

            return $deviceEntity;
        }
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
    }

    public function modifyWriteIntradayStep(IntradayStep $trackedEntity, Request $request)
    {
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

        /** @var IntradayStep[] $previousData */
        $trackerDate = $trackedEntity->getDate();
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        $trackerPatient = $trackedEntity->getPatient();
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        $thirdPartyService = $trackedEntity->getThirdPartyService();
        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
        if (!$trackedEntity->getTrackingDevice() instanceof TrackingDevice) {
            //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);
            $trackedEntity->setTrackingDevice($this->getTrackerEntry($trackedEntity, $request));
        }

        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

        $previousData = $this->em->getRepository(IntradayStep::class)
            ->findBy(['date' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService, 'trackingDevice' => $trackedEntity->getTrackingDevice()]);

        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        //$this->logManager->nxrInfo(__FILE__ . '@' . __LINE__);

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getValue() != $trackedEntity->getValue()) {
            $dataChanged = true;
            $previousData->setValue($trackedEntity->getValue());
        }

        if ($previousData->getTrackingDevice() !== $trackedEntity->getTrackingDevice()) {
            $dataChanged = true;
            $previousData->setTrackingDevice($trackedEntity->getTrackingDevice());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteCountDailyFloor(CountDailyFloor $trackedEntity, Request $request)
    {
        /** @var CountDailyFloor[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();
        if (!$trackedEntity->getTrackingDevice() instanceof TrackingDevice) {
            $trackedEntity->setTrackingDevice($this->getTrackerEntry($trackedEntity, $request));
        }

        $previousData = $this->em->getRepository(CountDailyFloor::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getValue() != $trackedEntity->getValue()) {
            $dataChanged = true;
            $previousData->setValue($trackedEntity->getValue());
        }

        if ($previousData->getTrackingDevice() !== $trackedEntity->getTrackingDevice()) {
            $dataChanged = true;
            $previousData->setTrackingDevice($trackedEntity->getTrackingDevice());
        }

        if (!is_numeric($previousData->getGoal()) && !is_numeric($trackedEntity->getGoal())) {
            $dataChanged = true;
            $previousData->setGoal($trackerPatient->getFloorGoal());
        } else if ($previousData->getGoal() != $trackedEntity->getGoal()) {
            $dataChanged = true;
            if (is_numeric($trackedEntity->getGoal())) {
                $previousData->setGoal($trackedEntity->getGoal());
            } else {
                $previousData->setGoal($trackerPatient->getFloorGoal());
            }
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteBodyWeight(BodyWeight $trackedEntity, Request $request)
    {
        /** @var BodyWeight[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();

        if (!$trackedEntity->getUnitOfMeasurement() instanceof UnitOfMeasurement) {
            $trackedEntity->setUnitOfMeasurement($this->getUnitOfMeasurementEntry($trackedEntity, $request));
        }

        if (!$trackedEntity->getPartOfDay() instanceof TrackingDevice) {
            $trackedEntity->setPartOfDay($this->getPartOfDay($trackedEntity->getDateTime()));
        }

        $previousData = $this->em->getRepository(BodyWeight::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getGoal() != $trackedEntity->getGoal()) {
            $dataChanged = true;
            $previousData->setGoal($trackedEntity->getGoal());
        }

        if ($previousData->getMeasurement() != $trackedEntity->getMeasurement()) {
            $dataChanged = true;
            $previousData->setPartOfDay($trackedEntity->getPartOfDay());
            $previousData->setMeasurement($trackedEntity->getMeasurement());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    /**
     * @param String $trackedDate
     * @param Request $request
     * @return PartOfDay|UnitOfMeasurement
     */
    private function getPartOfDay($trackedDate)
    {
        /** @var DateTime $trackedDate */
        $trackedDate = $trackedDate->format("U");
        $trackedDate = date("H", $trackedDate);

        if ($trackedDate >= 12 || $trackedDate <= 16) {
            $partOfDay = "afternoon";
        } else if ($trackedDate >= 17 && $trackedDate <= 20) {
            $partOfDay = "evening";
        } else if ($trackedDate >= 5 && $trackedDate <= 11) {
            $partOfDay = "morning";
        } else {
            $partOfDay = "night";
        }

        /** @var PartOfDay $deviceEntity */
        $deviceEntity = $this->em->getRepository(PartOfDay::class)
            ->findOneByName($partOfDay);

        if (!$deviceEntity) {
            $deviceEntity = new UnitOfMeasurement();
            $deviceEntity->setName($partOfDay);
        }

        $this->em->persist($deviceEntity);

        return $deviceEntity;
    }

    public function modifyWriteBodyBmi(BodyBmi $trackedEntity, Request $request)
    {
        /** @var BodyBmi[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();

        $previousData = $this->em->getRepository(BodyBmi::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getMeasurement() != $trackedEntity->getMeasurement()) {
            $dataChanged = true;
            $previousData->setMeasurement($trackedEntity->getMeasurement());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteBodyFat(BodyFat $trackedEntity, Request $request)
    {
        /** @var BodyFat[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();

        if (!$trackedEntity->getPartOfDay() instanceof TrackingDevice) {
            $trackedEntity->setPartOfDay($this->getPartOfDay($trackedEntity->getDateTime()));
        }

        if (!$trackedEntity->getUnitOfMeasurement() instanceof UnitOfMeasurement) {
            $trackedEntity->setUnitOfMeasurement($this->getUnitOfMeasurementEntry($trackedEntity, $request));
        }

        $previousData = $this->em->getRepository(BodyFat::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getGoal() != $trackedEntity->getGoal()) {
            $dataChanged = true;
            $previousData->setGoal($trackedEntity->getGoal());
        }

        if ($previousData->getMeasurement() != $trackedEntity->getMeasurement()) {
            $dataChanged = true;
            $previousData->setPartOfDay($trackedEntity->getPartOfDay());
            $previousData->setMeasurement($trackedEntity->getMeasurement());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteWaterIntake(WaterIntake $trackedEntity, Request $request)
    {
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

        if (!$trackedEntity->getPartOfDay() instanceof TrackingDevice) {
            $trackedEntity->setPartOfDay($this->getPartOfDay($trackedEntity->getDateTime()));
        }

//            $this->logManager->nxrInfo(__FILE__ . '@' . __LINE__ . ' = ' . $trackedEntity->getMeasurement());

        $previousData = $this->em->getRepository(WaterIntake::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'trackingDevice' => $trackingDevice]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getMeasurement() != $trackedEntity->getMeasurement()) {
            $dataChanged = true;
            $previousData->setMeasurement($trackedEntity->getMeasurement());
        }

        if ($previousData->getComment() != $trackedEntity->getComment()) {
            $dataChanged = true;
            $previousData->setComment($trackedEntity->getComment());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteCountDailyDistance(CountDailyDistance $trackedEntity, Request $request)
    {
        /** @var CountDailyDistance[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();

        $previousData = $this->em->getRepository(CountDailyDistance::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService]);

        if (!$trackedEntity->getUnitOfMeasurement() instanceof UnitOfMeasurement) {
            $trackedEntity->setUnitOfMeasurement($this->getUnitOfMeasurementEntry($trackedEntity, $request));
        }

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getValue() != $trackedEntity->getValue()) {
            $dataChanged = true;
            $previousData->setValue($trackedEntity->getValue());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteCountDailyCalories(CountDailyCalories $trackedEntity, Request $request)
    {
        /** @var CountDailyCalories[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $thirdPartyService = $trackedEntity->getThirdPartyService();

        $previousData = $this->em->getRepository(CountDailyCalories::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'thirdPartyService' => $thirdPartyService]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getValue() != $trackedEntity->getValue()) {
            $dataChanged = true;
            $previousData->setValue($trackedEntity->getValue());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    private function getUnitOfMeasurementEntry($trackedEntity, Request $request)
    {
        /** @noinspection PhpComposerExtensionStubsInspection */
        $postContents = json_decode($request->getContent(), TRUE);
        if (array_key_exists("x-unitOfMeasurement", $postContents)) {
            /** @var UnitOfMeasurement $deviceEntity */
            $deviceEntity = $this->em->getRepository(UnitOfMeasurement::class)
                ->findOneByName($postContents['x-unitOfMeasurement']);

            if (!$deviceEntity) {
                $deviceEntity = new UnitOfMeasurement();
                $deviceEntity->setName($postContents['x-unitOfMeasurement']);
            }

            $this->em->persist($deviceEntity);

            return $deviceEntity;
        }
    }

    public function modifyWriteCaffeineIntake(CaffeineIntake $trackedEntity, Request $request)
    {
        /** @var CaffeineIntake[] $previousData */
        $trackerDate = $trackedEntity->getDateTime();
        $trackerPatient = $trackedEntity->getPatient();
        $trackingDevice = $trackedEntity->getTrackingDevice();
        if (!$trackingDevice instanceof TrackingDevice) {
            $trackingDevice = $this->getTrackerEntry($trackedEntity, $request);
            $trackedEntity->setTrackingDevice($trackingDevice);
        }

        if (!$trackedEntity->getPartOfDay() instanceof TrackingDevice) {
            $trackedEntity->setPartOfDay($this->getPartOfDay($trackedEntity->getDateTime()));
        }

        $previousData = $this->em->getRepository(CaffeineIntake::class)
            ->findBy(['date_time' => $trackerDate, 'patient' => $trackerPatient, 'trackingDevice' => $trackingDevice]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getMeasurement() != $trackedEntity->getMeasurement()) {
            $dataChanged = true;
            $previousData->setMeasurement($trackedEntity->getMeasurement());
        }

        if ($previousData->getComment() != $trackedEntity->getComment()) {
            $dataChanged = true;
            $previousData->setComment($trackedEntity->getComment());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteTrackingDevice(TrackingDevice $trackedEntity, Request $request)
    {
        /** @var TrackingDevice[] $previousData */
        $trackerPatient = $trackedEntity->getPatient();
        $previousData = $this->em->getRepository(TrackingDevice::class)
            ->findBy(['patient' => $trackerPatient, 'remote_id' => $trackedEntity->getRemoteId()]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getName() != $trackedEntity->getName()) {
            $dataChanged = true;
            $previousData->setName($trackedEntity->getName());
        }

        if ($previousData->getBattery() != $trackedEntity->getBattery()) {
            $dataChanged = true;
            $previousData->setBattery($trackedEntity->getBattery());
        }

        if ($previousData->getLastSyncTime() !== $trackedEntity->getLastSyncTime()) {
            $dataChanged = true;
            $previousData->setLastSyncTime($trackedEntity->getLastSyncTime());
        }

        if ($previousData->getManufacturer() != $trackedEntity->getManufacturer()) {
            $dataChanged = true;
            $previousData->setManufacturer($trackedEntity->getManufacturer());
        }

        if ($previousData->getModel() != $trackedEntity->getModel()) {
            $dataChanged = true;
            $previousData->setModel($trackedEntity->getModel());
        } else if (empty($trackedEntity->getModel())) {
            $dataChanged = true;
            $previousData->setModel($trackedEntity->getName());
        }

        if ($previousData->getType() != $trackedEntity->getType()) {
            $dataChanged = true;
            $previousData->setType($trackedEntity->getType());
        }

        if ($previousData->getComment() != $trackedEntity->getComment() && !empty($trackedEntity->getComment())) {
            $dataChanged = true;
            $previousData->setComment($trackedEntity->getComment());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }

    public function modifyWriteExercise(Exercise $trackedEntity, Request $request)
    {
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
            ->findBy(['date_time' => $trackerStartDate, 'date_time_end' => $trackerEndDate, 'patient' => $trackerPatient, 'tracker' => $trackingDevice]);

        if (!$previousData) {
            return [TRUE, $trackedEntity];
        }

        $dataChanged = false;
        $previousData = $previousData[0];

        if ($previousData->getComment() != $trackedEntity->getComment()) {
            $dataChanged = true;
            $previousData->setComment($trackedEntity->getComment());
        }

        if ($previousData->getAltitudeGain() != $trackedEntity->getAltitudeGain()) {
            $dataChanged = true;
            $previousData->setAltitudeGain($trackedEntity->getAltitudeGain());
        }

        if ($previousData->getAltitudeLoss() != $trackedEntity->getAltitudeLoss()) {
            $dataChanged = true;
            $previousData->setAltitudeLoss($trackedEntity->getAltitudeLoss());
        }

        if ($previousData->getAltitudeMax() != $trackedEntity->getAltitudeMax()) {
            $dataChanged = true;
            $previousData->setAltitudeMax($trackedEntity->getAltitudeMax());
        }

        if ($previousData->getAltitudeMin() != $trackedEntity->getAltitudeMin()) {
            $dataChanged = true;
            $previousData->setAltitudeMin($trackedEntity->getAltitudeMin());
        }

        if ($previousData->getCalorie() != $trackedEntity->getCalorie()) {
            $dataChanged = true;
            $previousData->setCalorie($trackedEntity->getCalorie());
        }

        if ($previousData->getDistance() != $trackedEntity->getDistance()) {
            $dataChanged = true;
            $previousData->setDistance($trackedEntity->getDistance());
        }

        if ($previousData->getDuration() != $trackedEntity->getDuration()) {
            $dataChanged = true;
            $previousData->setDuration($trackedEntity->getDuration());
        }

        if ($previousData->getSpeedMax() != $trackedEntity->getSpeedMax()) {
            $dataChanged = true;
            $previousData->setSpeedMax($trackedEntity->getSpeedMax());
        }

        if ($previousData->getSpeedMean() != $trackedEntity->getSpeedMean()) {
            $dataChanged = true;
            $previousData->setSpeedMean($trackedEntity->getSpeedMean());
        }

        if ($dataChanged) {
            $this->em->flush();
        }

        return [FALSE, $trackedEntity];
    }
}