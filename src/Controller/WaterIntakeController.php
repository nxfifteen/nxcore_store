<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WaterIntakeController extends AbstractController
{
    /**
     * @Route("/water/intake/{id}", name="water_intake")
     */
    public function index($id)
    {
        return $this->json([
            'message' => "Smile " . $id,
            'path' => 'src/Controller/WaterIntakeController.php',
        ]);
    }
}
