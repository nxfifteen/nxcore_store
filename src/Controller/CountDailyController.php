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

    use App\Entity\BodyBmi;
    use App\Entity\BodyFat;
    use App\Entity\BodyWeight;
    use App\Entity\CountDailyFloor;
    use App\Entity\CountDailyStep;
    use App\Entity\Patient;
    use App\Entity\WaterIntake;
    use App\Repository\CountDailyFloorRepository;
    use DateTime;
    use Doctrine\Common\Collections\Criteria;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Constraints\Date;

    class CountDailyController extends AbstractController
    {
        /**
         * @Route("/json/{id}/count/daily/steps", name="count_daily_step")
         * @param String $id
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyStep( String $id )
        {
            return $this->countDailyStepDateTracker($id, date("Y-m-d"), -1);
        }

        /**
         * @Route("/json/{id}/count/daily/steps/{trackingDevice}", name="count_daily_step_trackingDevice")
         * @param String $id
         * @param int $trackingDevice
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyStepTracker(String $id, int $trackingDevice)
        {
            return $this->countDailyStepDateTracker($id, date("Y-m-d"), $trackingDevice);
        }
        /**
         * @Route("/json/{id}/count/daily/steps/{trackingDevice}/{date}", name="count_daily_step_date_trackingDevice")
         * @param String $id
         * @param String $date
         * @param int $trackingDevice
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyStepDateTracker(String $id, String $date, int $trackingDevice)
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $product = $this->getDoctrine()
                ->getRepository(CountDailyStep::class)
                ->findByDateRange($id, $date, $trackingDevice);

            $timeStampsInTrack = [];
            $timeStampsInTrack[ 'uuid' ] = $id;
            $timeStampsInTrack[ 'today' ] = $date;
            $timeStampsInTrack[ 'lastReading' ] = $date;
            $timeStampsInTrack[ 'sum' ] = 0;
            $timeStampsInTrack[ 'goal' ] = 0;
            $timeStampsInTrack[ 'values' ] = [];

            if ( count($product) > 0 ) {
                $goals = 0;

                /** @var CountDailyFloor[] $product */
                foreach ( $product as $item ) {
                    if ( is_numeric($item->getValue()) ) {
                        $timeStampsInTrack[ 'sum' ] = $timeStampsInTrack[ 'sum' ] + $item->getValue();
                        if (is_numeric($item->getGoal())) {
                            $goals = $goals + 1;
                            $timeStampsInTrack['goal'] = $timeStampsInTrack['goal'] + $item->getGoal();
                        }

                        $recordItem = [];
                        $recordItem[ 'dateTime' ] = $item->getDateTime()->format("H:i:s");
                        $recordItem[ 'value' ] = $item->getValue();
                        $recordItem['tracker'] = $item->getTrackingDevice()->getName();
                        $recordItem[ 'service' ] = $item->getThirdPartyService()->getName();

                        $timeStampsInTrack[ 'values' ][] = $recordItem;
                    }
                }
                if ( isset($item) ) {
                    $timeStampsInTrack[ 'lastReading' ] = $item->getDateTime()->format("H:i:s");
                }

                if ($goals > 0) {
                    $timeStampsInTrack['goal'] = $timeStampsInTrack['goal'] / $goals;
                } else {
                    $timeStampsInTrack['goal'] = 0;
                }
            }

            if ( $timeStampsInTrack[ 'goal' ] == 0 ) {
                /** @var Patient $patient */
                $patient = $this->getDoctrine()
                    ->getRepository(Patient::class)
                    ->findByUuid($id);

                if ( !is_null($patient) ) {
                    $timeStampsInTrack[ 'goal' ] = $patient->getStepGoal();
                } else {
                    $timeStampsInTrack[ 'goal' ] = 0;
                }
            }

            return $this->json($timeStampsInTrack);
        }

        /**
         * @Route("/json/{id}/count/daily/floors", name="count_daily_floor")
         * @param String $id
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyFloor( String $id )
        {
            return $this->countDailyFloorDate($id, date("Y-m-d"));
        }

        /**
         * @Route("/json/{id}/count/daily/floors/{date}", name="count_daily_floor_date")
         * @param String $id
         * @param String $date
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyFloorDate( String $id, String $date )
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $product = $this->getDoctrine()
                ->getRepository(CountDailyFloor::class)
                ->findByDateRange($id, $date);

            $timeStampsInTrack = [];
            $timeStampsInTrack[ 'uuid' ] = $id;
            $timeStampsInTrack[ 'today' ] = $date;
            $timeStampsInTrack[ 'lastReading' ] = $date;
            $timeStampsInTrack[ 'sum' ] = 0;
            $timeStampsInTrack[ 'goal' ] = 0;
            $timeStampsInTrack[ 'values' ] = [];

            if ( count($product) > 0 ) {
                /** @var CountDailyFloor[] $product */
                foreach ( $product as $item ) {
                    if ( is_numeric($item->getValue()) ) {
                        $timeStampsInTrack[ 'sum' ] = $timeStampsInTrack[ 'sum' ] + $item->getValue();
                        $timeStampsInTrack[ 'goal' ] = $timeStampsInTrack[ 'goal' ] + $item->getGoal();

                        $recordItem = [];
                        $recordItem[ 'dateTime' ] = $item->getDateTime()->format("H:i:s");
                        $recordItem[ 'value' ] = $item->getValue();
                        $recordItem[ 'service' ] = $item->getThirdPartyService()->getName();

                        $timeStampsInTrack[ 'values' ][] = $recordItem;
                    }
                }
                if ( isset($item) ) {
                    $timeStampsInTrack[ 'lastReading' ] = $item->getDateTime()->format("H:i:s");
                }

                $timeStampsInTrack[ 'goal' ] = $timeStampsInTrack[ 'goal' ] / count($product);
            }

            if ( $timeStampsInTrack[ 'goal' ] == 0 ) {
                /** @var Patient $patient */
                $patient = $this->getDoctrine()
                    ->getRepository(Patient::class)
                    ->findByUuid($id);

                if ( !is_null($patient) ) {
                    $timeStampsInTrack[ 'goal' ] = $patient->getFloorGoal();
                } else {
                    $timeStampsInTrack[ 'goal' ] = 0;
                }
            }

            return $this->json($timeStampsInTrack);
        }

        /**
         * @Route("/json/{id}/count/daily/water", name="count_daily_water")
         * @param String $id
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyWater( String $id )
        {
            return $this->countDailyWaterDate($id, date("Y-m-d"));
        }

        /**
         * @Route("/json/{id}/count/daily/water/{date}", name="count_daily_water_date")
         * @param String $id
         * @param String $date
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyWaterDate( String $id, String $date )
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $product = $this->getDoctrine()
                ->getRepository(WaterIntake::class)
                ->findByDateRange($id, $date);

            $timeStampsInTrack = [];
            $timeStampsInTrack[ 'uuid' ] = $id;
            $timeStampsInTrack[ 'today' ] = $date;
            $timeStampsInTrack[ 'lastReading' ] = $date;
            $timeStampsInTrack[ 'sum' ] = 0;
            $timeStampsInTrack[ 'goal' ] = 2000;
            $timeStampsInTrack[ 'values' ] = [];

            if ( count($product) > 0 ) {
                /** @var WaterIntake[] $product */
                foreach ( $product as $item ) {
                    if ( is_numeric($item->getMeasurement()) ) {
                        $timeStampsInTrack[ 'sum' ] = $timeStampsInTrack[ 'sum' ] + $item->getMeasurement();

                        $recordItem = [];
                        $recordItem[ 'dateTime' ] = $item->getDateTime()->format("H:i:s");
                        $recordItem[ 'value' ] = $item->getMeasurement();
                        $recordItem[ 'comment' ] = $item->getComment();
                        $recordItem[ 'service' ] = $item->getTrackingDevice()->getName();

                        $timeStampsInTrack[ 'values' ][] = $recordItem;
                    }
                }
                if ( isset($item) ) {
                    $timeStampsInTrack[ 'lastReading' ] = $item->getDateTime()->format("H:i:s");
                }
            }

            return $this->json($timeStampsInTrack);
        }

        /**
         * @Route("/json/{id}/count/daily/weight", name="count_daily_weight")
         * @param String $id
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyBody( String $id )
        {
            return $this->countDailyBodyDate($id, date("Y-m-d"));
        }

        /**
         * @Route("/json/{id}/count/daily/weight/{date}", name="count_daily_weight_date")
         * @param String $id
         * @param String $date
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function countDailyBodyDate( String $id, String $date )
        {
            /** @var BodyWeight[] $bodyWeight */
            /** @var BodyFat[] $bodyFat */
            /** @var BodyBmi[] $bodyBmi */

            /** @noinspection PhpUndefinedMethodInspection */
            $bodyWeight = $this->getDoctrine()
                ->getRepository(BodyWeight::class)
                ->findByDate($id, $date);
            if (!$bodyWeight) {
                $bodyWeight = $this->getDoctrine()
                    ->getRepository(BodyWeight::class)
                    ->getLastReading($id);
            }

            $bodyFat = $this->getDoctrine()
                ->getRepository(BodyFat::class)
                ->findByDate($id, $date);
            if (!$bodyFat) {
                $bodyFat = $this->getDoctrine()
                    ->getRepository(BodyFat::class)
                    ->getLastReading($id);
            }

            $bodyBmi = $this->getDoctrine()
                ->getRepository(BodyBmi::class)
                ->findByDate($id, $date);
            if (!$bodyBmi) {
                $bodyBmi = $this->getDoctrine()
                    ->getRepository(BodyBmi::class)
                    ->getLastReading($id);
            }

            $timeStampsInTrack = [];
            $timeStampsInTrack[ 'uuid' ] = $id;
            $timeStampsInTrack[ 'today' ] = $date;

            $bodyWeight = $bodyWeight[0];
            $timeStampsInTrack[ 'weight' ] = [];
            $timeStampsInTrack[ 'weight' ][ 'date' ] = $bodyWeight->getDateTime()->format("Y-m-d");
            $timeStampsInTrack[ 'weight' ][ 'measurement' ] = round($bodyWeight->getMeasurement() * 2.20462, 2);
            $timeStampsInTrack[ 'weight' ][ 'goal' ] = round($bodyWeight->getGoal() * 2.20462, 2);

            $bodyFat = $bodyFat[0];
            $timeStampsInTrack[ 'fat' ] = [];
            $timeStampsInTrack[ 'fat' ][ 'date' ] = $bodyWeight->getDateTime()->format("Y-m-d");
            $timeStampsInTrack[ 'fat' ][ 'measurement' ] = $bodyFat->getMeasurement();
            $timeStampsInTrack[ 'fat' ][ 'goal' ] = 21;

            $bodyBmi = $bodyBmi[0];
            $timeStampsInTrack[ 'bmi' ] = [];
            $timeStampsInTrack[ 'bmi' ][ 'date' ] = $bodyWeight->getDateTime()->format("Y-m-d");
            $timeStampsInTrack[ 'bmi' ][ 'measurement' ] = $bodyBmi->getMeasurement();
            $timeStampsInTrack[ 'bmi' ][ 'goal' ] = 22;

            return $this->json($timeStampsInTrack);
        }
    }
