<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Sofort\Payment\Gateway\Exception\ResponseException;

/**
 * Class Url
 * @package Sofort\Payment\Gateway\Helper
 */
class Url extends AbstractHelper
{
    const SOFORT_URL_NOTIFICATION = 'sofortueberweisung/payment/notification';

    const SOFORT_URL_ABORT = 'sofortueberweisung/payment/abort';

    const SOFORT_URL_SUCCESS = 'sofortueberweisung/payment/success';

    /**
     * @return string
     */
    public function getNotificationUrl($orderId = 0)
    {
        return $this->_getUrl(
            self::SOFORT_URL_NOTIFICATION,
            ['orderId' => $orderId]
        );
    }

    /**
     * @return string
     */
    public function getAbortUrl($orderId = 0)
    {
        return $this->_getUrl(
            self::SOFORT_URL_ABORT,
            ['orderId' => $orderId]
        );
    }

    /**
     * @return string
     */
    public function getSuccessUrl($orderId = 0)
    {
        return $this->_getUrl(
            self::SOFORT_URL_SUCCESS,
            ['orderId' => $orderId]
        );
    }
}
