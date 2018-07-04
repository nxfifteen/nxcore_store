<?php

namespace App\Controller;

use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyRelations;
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
        /** @var \App\Entity\Patient $patient */
        $patient = $this->getDoctrine()->getRepository(Patient::class)->findOneBy([ 'email' => $patientArray[ 2 ]]);
        if (!$patient) {
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
        $patient = $this->getDoctrine()->getRepository(Patient::class)->findOneBy(['email' => $patientArray[2]]);
        if (!$patient) {
            $entityManager = $this->getDoctrine()->getManager();

            $patient = new Patient();
            $patient->setFname($patientArray[ 0 ]);
            $patient->setLname($patientArray[ 1 ]);
            $patient->setEmail($patientArray[ 2 ]);
            $patient->setPassword($patientArray[ 3 ]);

            $patient->addThirdPartyRelation($this->createFitbitRelationShip($patient, $entityManager));

            $entityManager->persist($patient);
            $entityManager->flush();
        }

        return $this->json([
            'message' => "All went well " . $patient->getFname(),
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
        $patient = $this->getDoctrine()->getRepository(Patient::class)->findOneBy(['email' => $email]);
        if ($patient) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($patient);
            $entityManager->flush();

            return __LINE__;
        }

        return __LINE__;
    }

    /**
     * @param Patient                                    $patient
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @return \App\Entity\BodyWeight
     */
    private function createBodyWeight( $patient, $entityManager )
    {
        $bodyWeight = new BodyWeight();
        $bodyWeight->setPatient($patient);
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
     * @param Patient                                    $patient
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @return \App\Entity\BodyFat
     */
    private function createBodyFat( $patient, $entityManager )
    {
        $bodyFat = new BodyFat();
        $bodyFat->setPatient($patient);
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

    /**
     * @param Patient                                    $patient
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @return \App\Entity\ThirdPartyRelations
     */
    private function createFitbitRelationShip( $patient, $entityManager )
    {
        $bodyFat = new ThirdPartyRelations();
        $bodyFat->setPatient($patient);
        $bodyFat->setUsername("269VLG");

        $entityManager->persist($bodyFat);
        return $bodyFat;
    }
}
