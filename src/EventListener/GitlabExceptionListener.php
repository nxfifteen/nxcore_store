<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\EventListener;

use App\AppConstants;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class GitlabExceptionListener
 *
 * @package App\EventListener
 */
class GitlabExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getMessage] - " . $exception->getMessage());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getFile] - " . $exception->getFile());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getCode] - " . $exception->getCode());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getLine] - " . $exception->getLine());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getPrevious] - " . $exception->getPrevious());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getTraceAsString] - " . $exception->getTraceAsString());
//        AppConstants::writeToLog('debug_transform.txt', "[Exception::getTrace] - " . print_r($exception->getTrace(), true));

//        if (!$exception instanceof PublishedMessageException) {
//            return;
//        }
//
//        $code = $exception instanceof UserInputException ? 400 : 500;
//
//        $responseData = [
//            'error' => [
//                'code' => $code,
//                'message' => $exception->getMessage()
//            ]
//        ];
//
//        $event->setResponse(new JsonResponse($responseData, $code));
    }
}
