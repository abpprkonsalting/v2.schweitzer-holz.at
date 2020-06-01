<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Monolog\Logger;
use Sofort\Payment\Gateway\Helper\Url;
use Sofort\Payment\Helper\Config;
use Sofort\Payment\Helper\Version;

/**
 * Class UrlDataBuilder
 * @package Sofort\Payment\Gateway\Request
 */
class UrlDataBuilder implements BuilderInterface
{
    const SOFORT_SUCCESS_URL_KEY = 'success_url';

    const SOFORT_SUCCESS_LINK_REDIRECT_KEY = 'success_link_redirect';

    const SOFORT_ABORT_URL_KEY = 'abort_url';

    const SOFORT_NOTICATION_URLS_KEY = 'notification_urls';

    const SOFORT_NOTIFICATION_URL = 'notification_url';

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Version
     */
    protected $_versionHelper;

    /**
     * @var Url
     */
    protected $_urlHelper;

    /**
     * UrlDataBuilder constructor.
     * @param Config $configHelper
     * @param Version $versionHelper
     * @param Url $urlHelper
     */
    public function __construct(
        Config $configHelper,
        Version $versionHelper,
        Url $urlHelper
    )
    {
        $this->_versionHelper = $versionHelper;
        $this->_configHelper = $configHelper;
        $this->_urlHelper = $urlHelper;
    }

    /**
     * Generate base data
     *
     * @param array $buildSubject
     */
    public function build(array $buildSubject)
    {
        /**
         * @var PaymentDataObject $paymentData
         */
        $paymentData = $buildSubject['payment'];

        /**
         * @var OrderAdapterInterface $orderAdapter
         */
        $orderAdapter = $paymentData->getOrder();
        $orderIncrementId = $paymentData->getOrder()->getOrderIncrementId();

        $return = [];
        $return[self::SOFORT_SUCCESS_URL_KEY] = '';
        $return[self::SOFORT_SUCCESS_URL_KEY] = $this->_urlHelper->getSuccessUrl($orderIncrementId);
        $return[self::SOFORT_SUCCESS_LINK_REDIRECT_KEY] = 1;
        $return[self::SOFORT_ABORT_URL_KEY] = '';
        $return[self::SOFORT_ABORT_URL_KEY] = $this->_urlHelper->getAbortUrl($orderIncrementId);
        $return[self::SOFORT_NOTICATION_URLS_KEY] = '';
        $return[self::SOFORT_NOTICATION_URLS_KEY][self::SOFORT_NOTIFICATION_URL] = '';
        $return[self::SOFORT_NOTICATION_URLS_KEY][self::SOFORT_NOTIFICATION_URL]= $this->_urlHelper->getNotificationUrl($orderIncrementId);

        return $return;
    }

}
