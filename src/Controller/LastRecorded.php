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
    
    namespace App\Controller;

    use App\Entity\ApiAccessLog;
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
    use App\Entity\ThirdPartyRelations;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
    use DateTime;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class LastRecorded extends Controller
    {

        /**
         * @Route("/api/last/{patient}/{endpoint}", name="last_recorded_in_endpoint")
         * @param                            $patient
         * @param                            $endpoint
         * @param \App\Logger\SiteLogManager $logManager
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function entityClassSearch( $patient, $endpoint, SiteLogManager $logManager )
        {
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

        /**
         * @Route("/api/last/{patient}/{endpoint}/{service}", name="last_recorded_api_access")
         * @param                            $patient
         * @param                            $endpoint
         * @param                            $service
         * @param \App\Logger\SiteLogManager $logManager
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function apiAccessLogSearch( $patient, $endpoint, $service, SiteLogManager $logManager )
        {
//            $dbRecordSearch = $this->entityClassSearch($patient, $endpoint, $logManager);
//            return $dbRecordSearch;

            $apiAccessDoctrine = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findBy(['patient' => $patient, 'thirdPartyService' => $service, 'entity' => $endpoint], ['id' => 'DESC']);

            if (!$apiAccessDoctrine) {

                try {
                    $dbRecordSearch = $this->entityClassSearch($patient, $endpoint, $logManager);
                    return $dbRecordSearch;
                } catch (NotFoundHttpException $exception) {
                    $serviceRelationsDoctrine = $this->getDoctrine()
                        ->getRepository(ThirdPartyRelations::class)
                        ->findBy(['patient' => $patient, 'thirdPartyService' => $service]);

                    if (!$serviceRelationsDoctrine) {
                        throw $this->createNotFoundException(
                            'Class not found for ' . $endpoint
                        );
                    }

                    $serviceRelationsDoctrine = $serviceRelationsDoctrine[0];

                    /** @var ThirdPartyRelations $serviceRelationsDoctrine */
                    return $this->json([ $serviceRelationsDoctrine->getMemberSince()->format("Y-m-d H:i:s") ]);
                }

            } else {
                $apiAccessDoctrine = $apiAccessDoctrine[0];

                /** @var ApiAccessLog $apiAccessDoctrine */
                return $this->json([ $apiAccessDoctrine->getLastRetrieved()->format("Y-m-d H:i:s") ]);
            }
        }

        /**
         * @Route("/api/cooldown/{patient}/{endpoint}/{service}", name="cooldown_api_access")
         * @param                            $patient
         * @param                            $endpoint
         * @param                            $service
         * @param \App\Logger\SiteLogManager $logManager
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function apiAccessLogCooled( $patient, $endpoint, $service, SiteLogManager $logManager )
        {
            $apiAccessDoctrine = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findBy(['patient' => $patient, 'thirdPartyService' => $service, 'entity' => $endpoint], ['id' => 'DESC']);

            if (!$apiAccessDoctrine) {
                $this->json([ TRUE ]);
            } else {
                $apiAccessDoctrine = $apiAccessDoctrine[0];
                /** @var ApiAccessLog $apiAccessDoctrine */

                $currentDate = new DateTime ('now');
                $coolDownTill = $apiAccessDoctrine->getCooldown();

                if ( $coolDownTill->format("U") < $currentDate->format("U") ) {
                    return $this->json([ TRUE ]);
                } else {
                    return $this->json([ FALSE ]);
                }
            }
        }

        /**
         * @Route("/api/list/{patient}", name="last_recorded_api_list")
         * @param                            $patient
         * @param \App\Logger\SiteLogManager $logManager
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function apiAccessLogList( $patient, SiteLogManager $logManager )
        {
            $apiAccessDoctrine = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findBy(['patient' => $patient], ['entity' => 'ASC']);

            if (!$apiAccessDoctrine) {
                throw $this->createNotFoundException(
                    'Class not found for ' . $patient
                );
            } else {
                $returnJsonArray = [];
                /** @var ApiAccessLog $classDoctrine */
                foreach ( $apiAccessDoctrine as $classDoctrine ) {
                    $returnJsonArray[ $classDoctrine->getEntity() ] = [];

                    if (!is_null($classDoctrine->getLastPulled())) {
                        $returnJsonArray[ $classDoctrine->getEntity() ]['LastPulled'] = $classDoctrine->getLastPulled()->format("Y-m-d H:i:s");
                    } else {
                        $returnJsonArray[ $classDoctrine->getEntity() ]['LastPulled'] = NULL;
                    }

                    if (!is_null($classDoctrine->getLastRetrieved())) {
                        $returnJsonArray[ $classDoctrine->getEntity() ]['LastRetrieved'] = $classDoctrine->getLastRetrieved()->format("Y-m-d H:i:s");
                        $daysLeft = ( strtotime(date("Y-m-d")) - strtotime($classDoctrine->getLastRetrieved()->format("Y-m-d")) ) / ( 60 * 60 * 24 );
                        $returnJsonArray[ $classDoctrine->getEntity() ]['daysLeft'] = round($daysLeft, 0);
                        if ($returnJsonArray[ $classDoctrine->getEntity() ]['daysLeft'] <= 1) {
                            unset($returnJsonArray[ $classDoctrine->getEntity() ]);
                        }
                    } else {
                        $returnJsonArray[ $classDoctrine->getEntity() ]['LastRetrieved'] = NULL;
                        $returnJsonArray[ $classDoctrine->getEntity() ]['daysLeft'] = -1;
                        unset($returnJsonArray[ $classDoctrine->getEntity() ]);
                    }
                }

                return $this->json( $returnJsonArray );
            }
        }

    }