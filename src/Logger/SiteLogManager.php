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

        public function nxrDebug(String $msg)
        {
            $this->messageGenerator->nxrDebug($msg);
        }

        public function nxrError(String $msg)
        {
            $this->messageGenerator->nxrError($msg);
        }

    }