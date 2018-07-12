<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 09/07/2018
     * Time: 21:30
     */

    namespace App\Logger;

    use App\Service\MessageGenerator;

    class SiteLogManager
    {
        private $messageGenerator;

        public function __construct(MessageGenerator $messageGenerator)
        {
            $this->messageGenerator = $messageGenerator;
        }

        public function nxrCritical(String $msg)
        {
            $this->messageGenerator->nxrCritical($msg);
        }

        public function nxrInfo(String $msg)
        {
            $this->messageGenerator->nxrInfo($msg);
        }

        public function nxrError(String $msg)
        {
            $this->messageGenerator->nxrError($msg);
        }

    }