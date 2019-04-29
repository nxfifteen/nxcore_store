<?php

    namespace App\DataFixtures;

    use App\Entity\Patient;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    class PatientFixture extends Fixture
    {
        private $passwordEncoder;

        public function __construct( UserPasswordEncoderInterface $passwordEncoder )
        {
            $this->passwordEncoder = $passwordEncoder;
        }

        public function load( ObjectManager $manager )
        {
            $patient = new Patient();

            $patient->setPassword($this->passwordEncoder->encodePassword(
                $patient,
                'the_new_password'
            ));

            $manager->flush();
        }
    }
