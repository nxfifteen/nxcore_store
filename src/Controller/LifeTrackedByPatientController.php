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

    use App\Logger\SiteLogManager;
    use App\Entity\LifeTracked;
    use App\Entity\LifeTracker;
    use App\Service\MessageGenerator;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class LifeTrackedByPatientController extends Controller
    {
        /**
         * @Route("/api/life_trackeds/{patient}/index", name="life_tracked_by_patient")
         */
        public function index($patient)
        {
            $trackers = $this->getDoctrine()
                ->getRepository(LifeTracker::class)
                ->findBy(['patient' => $patient]);

            if (!$trackers) {
                throw $this->createNotFoundException(
                    'No product found for id '.$patient
                );
            }

            $alreadyTracked = [];
            foreach ( $trackers as $tracker ) {
                /** @var LifeTracker $tracker */
                /** @var LifeTracked $tracked */

                $trackeds = $this->getDoctrine()
                    ->getRepository(LifeTracked::class)
                    ->findBy(['tracker' => $tracker->getId()]);

                foreach ( $trackeds as $tracked ) {
                    $alreadyTracked[] = $tracked->getTracker()->getRemoteId() . "|" . $tracked->getDateTime()->format("Y-m-d H:i:s") . "|" . $patient;
                }
            }

            return $this->json($alreadyTracked);
        }

        /**
         * @Route("/api/life_trackeds/{patient}/last", name="life_tracked_by_patient_last")
         * @param                            $patient
         * @param \App\Logger\SiteLogManager $logManager
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function last($patient, SiteLogManager $logManager)
        {
            $trackers = $this->getDoctrine()
                ->getRepository(LifeTracker::class)
                ->findBy(['patient' => $patient]);

            if (!$trackers) {
                throw $this->createNotFoundException(
                    'No product found for id '.$patient
                );
            }

            $alreadyTracked = [];
            foreach ( $trackers as $tracker ) {
                /** @var LifeTracker $tracker */
                /** @var LifeTracked $tracked */

                $trackeds = $this->getDoctrine()
                    ->getRepository(LifeTracked::class)
                    ->findBy(['tracker' => $tracker->getId()], ['date_time' => 'DESC']);

                if (count($trackeds) > 0) {

                    foreach ( $trackeds as $tracked ) {
                        $alreadyTracked[ strtotime($tracked->getDateTime()->format("Y-m-d H:i:s")) ] = [
                            'stamp' => strtotime($tracked->getDateTime()->format("Y-m-d H:i:s")),
                            "human" => $tracked->getDateTime()->format("Y-m-d H:i:s")
                        ];
                    }
                }
            }

            ksort($alreadyTracked);
            if (count($alreadyTracked) == 0) {
                $logManager->nxrInfo("Patient " . $patient . " has no life tracks");
                return $this->json([0, 0]);
            } else {
                $returnValue = array_pop($alreadyTracked);

                $logManager->nxrInfo("Patient " . $patient . "'s last track was " . $returnValue['human']);
                return $this->json([$returnValue['stamp'], $returnValue['human']]);
            }
        }
    }