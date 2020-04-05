<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2020. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\AppConstants;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class GitlabExceptionListener
{
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
