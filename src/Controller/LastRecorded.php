<?php
    namespace App\Controller;

    use App\Entity\BodyWeight;
    use App\Entity\CountDailyCalories;
    use App\Entity\CountDailyDistance;
    use App\Entity\CountDailyElevation;
    use App\Entity\CountDailyFloor;
    use App\Entity\CountDailyStep;
    use App\Entity\IntradayStep;
    use App\Entity\MinDailyFairly;
    use App\Entity\MinDailyLightly;
    use App\Entity\MinDailySedentary;
    use App\Entity\MinDailyVery;
    use App\Entity\NutritionInformation;
    use App\Entity\SleepEpisode;
    use App\Entity\SportActivity;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class LastRecorded extends Controller
    {

        /**
         * @Route("/api/last/{patient}/{endpoint}", name="last_recorded_in_endpoint")
         * @param                            $patient
         * @param                            $endpoint
         * @param \App\Logger\SiteLogManager $logManager
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function last($patient, $endpoint, SiteLogManager $logManager)
        {
            $logManager->nxrInfo("Last entry for $patient in $endpoint was:");

            $trackers = null;
            switch ($endpoint) {
                case "BodyWeight":
                    $trackers = $this->getDoctrine()
                        ->getRepository(BodyWeight::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "IntradayStep":
                    $trackers = $this->getDoctrine()
                        ->getRepository(IntradayStep::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "CountDailyStep":
                    $trackers = $this->getDoctrine()
                        ->getRepository(CountDailyStep::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "CountDailyFloor":
                    $trackers = $this->getDoctrine()
                        ->getRepository(CountDailyFloor::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "CountDailyElevation":
                    $trackers = $this->getDoctrine()
                        ->getRepository(CountDailyElevation::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "CountDailyDistance":
                    $trackers = $this->getDoctrine()
                        ->getRepository(CountDailyDistance::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "CountDailyCalories":
                    $trackers = $this->getDoctrine()
                        ->getRepository(CountDailyCalories::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "SleepEpisode":
                    $trackers = $this->getDoctrine()
                        ->getRepository(SleepEpisode::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "SportActivity":
                    $trackers = $this->getDoctrine()
                        ->getRepository(SportActivity::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "MinDailyVery":
                    $trackers = $this->getDoctrine()
                        ->getRepository(MinDailyVery::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "MinDailyFairly":
                    $trackers = $this->getDoctrine()
                        ->getRepository(MinDailyFairly::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "MinDailyLightly":
                    $trackers = $this->getDoctrine()
                        ->getRepository(MinDailyLightly::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "MinDailySedentary":
                    $trackers = $this->getDoctrine()
                        ->getRepository(MinDailySedentary::class)
                        ->findBy(['patient' => $patient], ['id' => 'DESC']);
                    break;
                case "NutritionInformation":
                    /** @noinspection PhpUndefinedMethodInspection */
                    $trackers = $this->getDoctrine()
                        ->getRepository(NutritionInformation::class)
                        ->findLastMeal($patient);
                    break;
                case "WaterInformation":
                    /** @noinspection PhpUndefinedMethodInspection */
                    $trackers = $this->getDoctrine()
                        ->getRepository(NutritionInformation::class)
                        ->findLastWater($patient);
                    break;
            }

            if (!$trackers) {
                throw $this->createNotFoundException(
                    'Class not found for ' . $endpoint
                );
            } else {
                /** @var BodyWeight $returnObject */
                $returnObject = $trackers[0];
                $returnDate = "";
                if (method_exists($returnObject, "getDateTime")) {
                    $returnDate = $returnObject->getDateTime()->format("Y-m-d H:i:s");
                } else if (method_exists($returnObject, "getDate")) {
                    $returnDate = $returnObject->getDate()->format("Y-m-d H:i:s");
                } else if (method_exists($returnObject, "getStartTime")) {
                    $returnDate = $returnObject->getStartTime()->format("Y-m-d H:i:s");
                }

                return $this->json([ $returnDate ]);
            }
        }
    }