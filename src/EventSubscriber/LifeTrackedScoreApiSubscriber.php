<?php
    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\LifeTracked;
    use App\Service\MessageGenerator;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class LifeTrackedScoreApiSubscriber implements EventSubscriberInterface
    {

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
                KernelEvents::VIEW => [['newLifeTracked', EventPriorities::PRE_WRITE]],
            ];
        }

        public function newLifeTracked(GetResponseForControllerResultEvent $event) {
            $trackedEntity = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            if (!$trackedEntity instanceof LifeTracked || Request::METHOD_POST !== $method) {
                return;
            }


        }
    }