<?php
    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\LifeTracked;
    use App\Entity\LifeTrackerScore;
    use App\Logger\SiteLogManager;
    use App\Service\MessageGenerator;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class LifeTrackedSubscriber implements EventSubscriberInterface
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

            if (!$trackedEntity instanceof LifeTracked || Request::METHOD_POST !== $method) {
                return $trackedEntity;
            }

            $trackerId = $trackedEntity->getTracker()->getId();
            $trackerScoringRules = $this->em->getRepository(LifeTrackerScore::class)
                ->findBy(['lifeTracker' => $trackerId], ['cond' => 'ASC']);

            if ($trackerScoringRules) {
                $this->logManager->nxrInfo(count($trackerScoringRules) . " scoring rules for " . $trackedEntity->getTracker()->getName());

                /** @var LifeTrackerScore $trackerScoringRule */
                foreach ( $trackerScoringRules as $trackerScoringRule ) {
                    $this->logManager->nxrInfo(" Looking for " . $trackedEntity->getValue() . " to be " . $trackerScoringRule->getCond() . " " . $trackerScoringRule->getCompare());

                    if ($trackerScoringRule->getCond() == "lt" && $trackedEntity->getValue() < $trackerScoringRule->getCompare()) {
                        $this->logManager->nxrInfo("  Set tracker score to " . $trackerScoringRule->getCharge() . " on " . __LINE__);
                        $trackedEntity->setScore($trackerScoringRule->getCharge());
                    } else if ($trackerScoringRule->getCond() == "lte" && $trackedEntity->getValue() <= $trackerScoringRule->getCompare()) {
                        $this->logManager->nxrInfo("  Set tracker score to " . $trackerScoringRule->getCharge() . " on " . __LINE__);
                        $trackedEntity->setScore($trackerScoringRule->getCharge());
                    } else if ($trackerScoringRule->getCond() == "eq" && $trackedEntity->getValue() == $trackerScoringRule->getCompare()) {
                        $this->logManager->nxrInfo("  Set tracker score to " . $trackerScoringRule->getCharge() . " on " . __LINE__);
                        $trackedEntity->setScore($trackerScoringRule->getCharge());
                    } else if ($trackerScoringRule->getCond() == "gt" && $trackedEntity->getValue() > $trackerScoringRule->getCompare()) {
                        $this->logManager->nxrInfo("  Set tracker score to " . $trackerScoringRule->getCharge() . " on " . __LINE__);
                        $trackedEntity->setScore($trackerScoringRule->getCharge());
                    } else if ($trackerScoringRule->getCond() == "gte" && $trackedEntity->getValue() >= $trackerScoringRule->getCompare()) {
                        $this->logManager->nxrInfo("  Set tracker score to " . $trackerScoringRule->getCharge() . " on " . __LINE__);
                        $trackedEntity->setScore($trackerScoringRule->getCharge());
                    }
                }

            } else {
                $this->logManager->nxrInfo("No special scoring rules for " . $trackedEntity->getTracker()->getName());
            }

            return $trackedEntity;
        }
    }