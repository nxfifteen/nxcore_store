<?php

namespace App\Controller;

use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\Patient;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PatientController extends Controller
{
    /**
     * @Route("/patient", name="patient")
     */
    public function index()
    {
        $patientArray = ["Stuart", "Anderson", "stuart@anderson.eu.com", "123456"];
        /** @var \App\Entity\Patient $product */
        $product = $this->getDoctrine()->getRepository(Patient::class)->findOneBy([ 'email' => $patientArray[ 2 ]]);
        if (!$product) {
            $this->create($patientArray);

            return $this->json([
                'message' => "Okay",
                'path' => 'src/Controller/PatientController.php',
            ]);
        } else {

            return $this->json([
                'message' => "Done",
                'path' => 'src/Controller/PatientController.php',
            ]);
        }
    }

    /**
     * @Route("/patient/create", name="patient_create")
     */
    public function create($patientArray)
    {
        $product = $this->getDoctrine()->getRepository(Patient::class)->findOneBy(['email' => $patientArray[2]]);
        if (!$product) {
            $entityManager = $this->getDoctrine()->getManager();

            $product = new Patient();
            $product->setFname($patientArray[ 0 ]);
            $product->setLname($patientArray[ 1 ]);
            $product->setEmail($patientArray[ 2 ]);
            $product->setPassword($patientArray[ 3 ]);

            $bodyWeight = new BodyWeight();
            $bodyWeight->setPatient($product);
            $bodyWeight->setDatetime(new \DateTime());
            $bodyWeight->setMeasurement(rand(200, 500));
            $bodyWeight->setUnit("lb");
            $bodyWeight->setPartOfDay("night");

            $entityManager->persist($bodyWeight);
            $product->addBodyWeight($bodyWeight);

            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->json([
            'message' => "All went well " . $product->getFname(),
            'path' => 'src/Controller/PatientController.php',
        ]);
    }

    /**
     * @Route("/patient/delete", name="patient_delete")
     */
    public function delete()
    {
        $exitLine = $this->deletePatient('stuart@anderson.eu.com');
        return $this->json([
            'message' => "Removed patient (" . $exitLine . ")",
            'path' => 'src/Controller/PatientController.php',
        ]);
    }

    private function deletePatient( $email )
    {
        $product = $this->getDoctrine()->getRepository(Patient::class)->findOneBy(['email' => $email]);
        if ($product) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($product);
            $entityManager->flush();

            return __LINE__;
        }

        return __LINE__;
    }
}
