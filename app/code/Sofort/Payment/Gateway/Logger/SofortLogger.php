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

/**
 * Class SofortLogger
 * @package Sofort\Payment\Gateway\Logger
 */
class SofortLogger extends \Monolog\Logger implements SofortLoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function info($message, array $context = array())
    {
        $message = print_r($message, true);

        return parent::info($message, $context);
    }

}
