<?php
    namespace App\Controller;

    use App\Entity\BodyWeight;
    use App\Entity\CountDailyCalories;
    use App\Entity\CountDailyDistance;
    use App\Entity\CountDailyElevation;
    use App\Entity\CountDailyFloor;
    use App\Entity\CountDailyStep;
    use App\Entity\IntradayStep;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
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
            }

            if (!$trackers) {
                throw $this->createNotFoundException(
                    'Class not found for ' . $endpoint
                );
            } else {
                /** @var BodyWeight $returnValue */
                $returnValue = $trackers[0];
                $returnDate = "";
                if (method_exists($returnValue, "getDateTime")) {
                    $returnDate = $returnValue->getDateTime()->format("Y-m-d H:i:s");
                } else if (method_exists($returnValue, "getDate")) {
                    $returnDate = $returnValue->getDate()->format("Y-m-d H:i:s");
                }

                return $this->json([ $returnDate ]);
            }
        }
    }