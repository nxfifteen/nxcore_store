<?php

namespace App\Controller;

use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\UnitOfMeasurement;
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

            $product->addBodyWeight($this->createBodyWeight($product, $entityManager));
            $product->addBodyFat($this->createBodyFat($product, $entityManager));

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

    /**
     * @param Patient                                    $product
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @return \App\Entity\BodyWeight
     */
    private function createBodyWeight( $product, $entityManager )
    {
        $bodyWeight = new BodyWeight();
        $bodyWeight->setPatient($product);
        $bodyWeight->setDatetime(new \DateTime());
        $bodyWeight->setMeasurement(rand(200, 500));

        $unit = $this->getDoctrine()->getRepository(UnitOfMeasurement::class)->findOneBy(['name' => "lb"]);
        /** @var UnitOfMeasurement $unit */
        $bodyWeight->setUnitOfMeasurement($unit);

        $partOfDay = $this->getDoctrine()->getRepository(PartOfDay::class)->findOneBy(['name' => "night"]);
        /** @var PartOfDay $partOfDay */
        $bodyWeight->setPartOfDay($partOfDay);

        $entityManager->persist($bodyWeight);
        return $bodyWeight;
    }

    /**
     * @param Patient                                    $product
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @return \App\Entity\BodyFat
     */
    private function createBodyFat( $product, $entityManager )
    {
        $bodyFat = new BodyFat();
        $bodyFat->setPatient($product);
        $bodyFat->setDatetime(new \DateTime());
        $bodyFat->setMeasurement(rand(5, 90));

        $unit = $this->getDoctrine()->getRepository(UnitOfMeasurement::class)->findOneBy(['name' => "%"]);
        /** @var UnitOfMeasurement $unit */
        $bodyFat->setUnitOfMeasurement($unit);

        $partOfDay = $this->getDoctrine()->getRepository(PartOfDay::class)->findOneBy(['name' => "night"]);
        /** @var PartOfDay $partOfDay */
        $bodyFat->setPartOfDay($partOfDay);

        $entityManager->persist($bodyFat);
        return $bodyFat;
    }
}
