<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class SofortLoggerHandler
 * @package Sofort\Payment\Gateway\Logger
 */
class SofortLoggerHandler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/sofort.log';
}
