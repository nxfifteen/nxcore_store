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
    
    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\Reward;
    use App\Logger\SiteLogManager;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class RewardSubscriber implements EventSubscriberInterface
    {

        private $em;
        private $logManager;

        public function __construct(EntityManagerInterface $em, SiteLogManager $logManager)
        {
            $this->em = $em;
            $this->logManager = $logManager;
        }

        /**
         * Returns an array of event names this subscriber wants to listen to.
         *
         * The array keys are event names and the value can be:
         *
         *  * The method name to call (priority defaults to 0)
         *  * An array composed of the method name to call and the priority
         *  * An array of arrays composed of the method names to call and respective
         *    priorities, or 0 if unset
         *
         * For instance:
         *
         *  * array('eventName' => 'methodName')
         *  * array('eventName' => array('methodName', $priority))
         *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['newLifeTracked', EventPriorities::PRE_WRITE],
            ];
        }

        public function newLifeTracked(GetResponseForControllerResultEvent $event) {
            $trackedEntity = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            if (!$trackedEntity instanceof Reward || Request::METHOD_POST !== $method) {
                return $trackedEntity;
            }

            $rewardImagePath = __DIR__ . "/../../public/cache/rewards";
            $fileSystem = new Filesystem();
            if (!$fileSystem->exists($rewardImagePath . '/' . basename($trackedEntity->getImage()))) {
                if (!$fileSystem->exists($rewardImagePath)) {
                    $this->logManager->nxrInfo("Creating Reward Image folder");
                    $fileSystem->mkdir($rewardImagePath);
                }
                if ($fileSystem->exists($rewardImagePath)) {
                    $this->logManager->nxrInfo("Downloading Reward Image for " . $trackedEntity->getName());
                    $fileSystem->dumpFile($rewardImagePath . '/' . basename($trackedEntity->getImage()), file_get_contents($trackedEntity->getImage()));
                }
            }

            return $trackedEntity;
        }
    }