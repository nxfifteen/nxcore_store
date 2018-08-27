<?php
    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BodyBmi;
    use App\Entity\BodyFat;
    use App\Entity\BodyWeight;
    use App\Entity\CountDailyCalories;
    use App\Entity\CountDailyDistance;
    use App\Entity\CountDailyElevation;
    use App\Entity\CountDailyFloor;
    use App\Entity\CountDailyStep;
    use App\Entity\IntradayStep;
    use App\Entity\LifeTracked;
    use App\Entity\LifeTrackerScore;
    use App\Entity\MinDailyFairly;
    use App\Entity\MinDailyLightly;
    use App\Entity\MinDailySedentary;
    use App\Entity\MinDailyVery;
    use App\Entity\NutritionInformation;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class WebhookSubscriber implements EventSubscriberInterface
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
         *  * array('eventName' => 'methodName')
         *  * array('eventName' => array('methodName', $priority))
         *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['notifyWebhook', EventPriorities::PRE_WRITE],
            ];
        }

        public function notifyWebhook(GetResponseForControllerResultEvent $event) {
            $trackedEntity = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            if (Request::METHOD_POST !== $method) {
                return $trackedEntity;
            }

            $entityClass = str_ireplace("App\\Entity\\","", get_class($trackedEntity));
            if (!$trackedEntity instanceof CountDailyCalories &&
                !$trackedEntity instanceof CountDailyDistance &&
                !$trackedEntity instanceof CountDailyElevation &&
                !$trackedEntity instanceof CountDailyFloor &&
                !$trackedEntity instanceof CountDailyStep &&
                !$trackedEntity instanceof IntradayStep &&
                !$trackedEntity instanceof LifeTracked &&
                !$trackedEntity instanceof MinDailyFairly &&
                !$trackedEntity instanceof MinDailyLightly &&
                !$trackedEntity instanceof MinDailySedentary &&
                !$trackedEntity instanceof MinDailyVery &&
                !$trackedEntity instanceof NutritionInformation &&
                !$trackedEntity instanceof BodyWeight &&
                !$trackedEntity instanceof BodyFat &&
                !$trackedEntity instanceof BodyBmi) {
                $this->logManager->nxrInfo("Unknown write event from " . $entityClass);
                return $trackedEntity;
            }

            $this->logManager->nxrInfo("Event write from " . $entityClass);

            if (!array_key_exists("WEBHOOK_POSTWRITE", $_SERVER)) {
                return $trackedEntity;
            }

            $content = null;
            if ($trackedEntity instanceof CountDailyCalories) {
                $content = $this->buildJsonCountDailyCalories($trackedEntity);
            } elseif ($trackedEntity instanceof CountDailyDistance) {
                $content = $this->buildJsonCountDailyDistance($trackedEntity);
            } elseif ($trackedEntity instanceof CountDailyElevation) {
                $content = $this->buildJsonCountDailyElevation($trackedEntity);
            } elseif ($trackedEntity instanceof CountDailyFloor) {
                $content = $this->buildJsonCountDailyFloor($trackedEntity);
            } elseif ($trackedEntity instanceof CountDailyStep) {
                $content = $this->buildJsonCountDailyStep($trackedEntity);
            } elseif ($trackedEntity instanceof IntradayStep) {
                $content = $this->buildJsonIntradayStep($trackedEntity);
            } elseif ($trackedEntity instanceof LifeTracked) {
                $content = $this->buildJsonLifeTracked($trackedEntity);
            } elseif ($trackedEntity instanceof MinDailyFairly) {
                $content = $this->buildJsonMinDailyFairly($trackedEntity);
            } elseif ($trackedEntity instanceof MinDailyLightly) {
                $content = $this->buildJsonMinDailyLightly($trackedEntity);
            } elseif ($trackedEntity instanceof MinDailySedentary) {
                $content = $this->buildJsonMinDailySedentary($trackedEntity);
            } elseif ($trackedEntity instanceof MinDailyVery) {
                $content = $this->buildJsonMinDailyVery($trackedEntity);
            } elseif ($trackedEntity instanceof NutritionInformation) {
                $content = $this->buildJsonNutritionInformation($trackedEntity);
            } elseif ($trackedEntity instanceof BodyWeight) {
                $content = $this->buildJsonBodyWeight($trackedEntity);
            } elseif ($trackedEntity instanceof BodyFat) {
                $content = $this->buildJsonBodyFat($trackedEntity);
            } elseif ($trackedEntity instanceof BodyBmi) {
                $content = $this->buildJsonBodyBmi($trackedEntity);
            }

            if (!is_null($content)) {
                $this->makeWebCall($content);
            }

            return $trackedEntity;
        }

        private function makeWebCall($dataJson) {
            $url = $_SERVER['WEBHOOK_POSTWRITE'];

            $this->logManager->nxrInfo("Sending webhook call to " . $url);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataJson);

            curl_exec($curl);

            curl_close($curl);
        }

        /**
         * @param LifeTracked $trackedEntity
         * @return string
         */
        private function buildJsonLifeTracked( $trackedEntity )
        {
            return json_encode([
                "Id" => "LifeTracked",
                "User" => $trackedEntity->getTracker()->getPatient()->getId(),
                "Name" => $trackedEntity->getTracker()->getName(),
                "Value" => $trackedEntity->getValue(),
                "Score" => $trackedEntity->getScore(),
                "DateTime" => $trackedEntity->getDateTime()->format("Y-m-d")
            ]);
        }

        /**
         * @param NutritionInformation $trackedEntity
         * @return string
         */
        private function buildJsonNutritionInformation($trackedEntity) {
            return json_encode([
                "Id" => "NutritionInformation",
                "User" => $trackedEntity->getPatient()->getId(),
                "DateTime" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Name" => $trackedEntity->getName(),
                "Water" => $trackedEntity->getWater(),
                "Meal" => $trackedEntity->getMeal(),
                "Brand" => $trackedEntity->getBrand(),
                "Value" => $trackedEntity->getCalories(),
                "Carbs" => $trackedEntity->getCarbs(),
                "Fat" => $trackedEntity->getFat(),
                "Fiber" => $trackedEntity->getFiber(),
                "Protein" => $trackedEntity->getProtein(),
                "Sodium" => $trackedEntity->getSodium()
            ]);
        }

        /**
         * @param IntradayStep $trackedEntity
         * @return string
         */
        private function buildJsonIntradayStep( $trackedEntity )
        {
            return json_encode([
                "Id" => "IntradayStep",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDate()->format("Y-m-d"),
                "Hour" => $trackedEntity->getHour(),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param CountDailyCalories $trackedEntity
         * @return string
         */
        private function buildJsonCountDailyCalories( $trackedEntity )
        {
            return json_encode([
                "Id" => "CountDailyCalories",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param CountDailyDistance $trackedEntity
         * @return string
         */
        private function buildJsonCountDailyDistance( $trackedEntity )
        {
            return json_encode([
                "Id" => "CountDailyDistance",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param CountDailyElevation $trackedEntity
         * @return string
         */
        private function buildJsonCountDailyElevation( $trackedEntity )
        {
            return json_encode([
                "Id" => "CountDailyElevation",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param CountDailyFloor $trackedEntity
         * @return string
         */
        private function buildJsonCountDailyFloor( $trackedEntity )
        {
            return json_encode([
                "Id" => "CountDailyFloor",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param CountDailyStep $trackedEntity
         * @return string
         */
        private function buildJsonCountDailyStep( $trackedEntity )
        {
            return json_encode([
                "Id" => "CountDailyStep",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param MinDailyFairly $trackedEntity
         * @return string
         */
        private function buildJsonMinDailyFairly( $trackedEntity )
        {
            return json_encode([
                "Id" => "MinDailyFairly",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param MinDailyLightly $trackedEntity
         * @return string
         */
        private function buildJsonMinDailyLightly( $trackedEntity )
        {
            return json_encode([
                "Id" => "MinDailyLightly",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param MinDailySedentary $trackedEntity
         * @return string
         */
        private function buildJsonMinDailySedentary( $trackedEntity )
        {
            return json_encode([
                "Id" => "MinDailySedentary",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param MinDailyVery $trackedEntity
         * @return string
         */
        private function buildJsonMinDailyVery( $trackedEntity )
        {
            return json_encode([
                "Id" => "MinDailyVery",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Value" => $trackedEntity->getValue()
            ]);
        }

        /**
         * @param BodyWeight $trackedEntity
         * @return string
         */
        private function buildJsonBodyWeight( $trackedEntity )
        {
            return json_encode([
                "Id" => "BodyWeight",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Measurement" => $trackedEntity->getMeasurement(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param BodyFat $trackedEntity
         * @return string
         */
        private function buildJsonBodyFat( $trackedEntity )
        {
            return json_encode([
                "Id" => "BodyFat",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Measurement" => $trackedEntity->getMeasurement(),
                "Goal" => $trackedEntity->getGoal()
            ]);
        }

        /**
         * @param BodyBmi $trackedEntity
         * @return string
         */
        private function buildJsonBodyBmi( $trackedEntity )
        {
            return json_encode([
                "Id" => "BodyBmi",
                "User" => $trackedEntity->getPatient()->getId(),
                "Date" => $trackedEntity->getDateTime()->format("Y-m-d"),
                "Measurement" => $trackedEntity->getMeasurement()
            ]);
        }
    }