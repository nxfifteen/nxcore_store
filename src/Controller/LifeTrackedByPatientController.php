<?php
    namespace App\Controller;

    use App\Entity\LifeTracked;
    use App\Entity\LifeTracker;
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
                    ->findBy(['lifeTracker' => $tracker->getId()]);

                foreach ( $trackeds as $tracked ) {
                    $alreadyTracked[] = md5($tracked->getLifeTracker()->getRemoteId() . "|" . $tracked->getDateTime()->format("Y-m-d H:i:s") . "|" . $tracked->getValue());
                }
            }

            return $this->json($alreadyTracked);
        }

        /**
         * @Route("/api/life_trackeds/{patient}/last", name="life_tracked_by_patient_last")
         */
        public function last($patient)
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
                    ->findBy(['lifeTracker' => $tracker->getId()], ['date_time' => 'DESC']);

                if (count($trackeds) > 0) {
                    $tracked = $trackeds[ count($trackeds) - 1 ];
                    $alreadyTracked[] = strtotime($tracked->getDateTime()->format("Y-m-d H:i:s"));
                }
            }

            sort($alreadyTracked);

            return $this->json([$alreadyTracked[count($alreadyTracked) - 1]]);
        }
    }