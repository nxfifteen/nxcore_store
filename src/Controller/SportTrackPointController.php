<?php
    namespace App\Controller;

    use App\Entity\SportTrackPoint;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class SportTrackPointController extends Controller
    {
        /**
         * @Route("/api/sport_track_points/{id}/index", name="sport_track_point")
         */
        public function index($id)
        {
            $product = $this->getDoctrine()
                ->getRepository(SportTrackPoint::class)
                ->findBy(['sportTrack' => $id]);

            if (!$product) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }

            $timeStampsInTrack = [];
            foreach ( $product as $item ) {
                /** @var SportTrackPoint $item */
                $timeStampsInTrack[] = $item->getTime()->format("c");
            }

            return $this->json($timeStampsInTrack);
        }
    }