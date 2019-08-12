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
                return $this->json([]);
            }

            $timeStampsInTrack = [];
            foreach ( $product as $item ) {
                /** @var SportTrackPoint $item */
                $timeStampsInTrack[] = $item->getTime()->format("c");
            }

            return $this->json($timeStampsInTrack);
        }
    }