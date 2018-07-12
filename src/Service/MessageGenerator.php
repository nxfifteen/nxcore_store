<?php
    namespace App\Service;

    use Psr\Log\LoggerInterface;

    class MessageGenerator
    {
        private $logger;

        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

        public function nxrCritical(String $msg)
        {
            $this->logger->critical($msg);
        }

        public function nxrInfo(String $msg)
        {
            $this->logger->info($msg);
        }

        public function nxrError(String $msg)
        {
            $this->logger->error($msg);
        }
    }