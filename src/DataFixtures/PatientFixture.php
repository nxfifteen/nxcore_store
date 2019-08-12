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
