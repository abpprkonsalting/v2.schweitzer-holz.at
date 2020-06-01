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


use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Sofort\Payment\Helper\Config;

/**
 * Class OrderDataBuilder
 * @package Sofort\Payment\Gateway\Request
 */
class OrderDataBuilder implements BuilderInterface
{
    const SOFORT_NODE_AMOUNT = 'amount';

    const SOFORT_NODE_CURRENCY_CODE = 'currency_code';

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * OrderDataBuilder constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    )
    {
        $this->_configHelper = $configHelper;
    }

    /**
     * Generate base data
     *
     * @param array $buildSubject
     */
    public function build(array $buildSubject)
    {
        $return = [];


        /**
         * @var PaymentDataObject $payment
         */
        $payment = $buildSubject['payment'];

//        $return[self::SOFORT_NODE_AMOUNT] = $buildSubject['amount'];
        $return[self::SOFORT_NODE_AMOUNT] = $payment->getOrder()->getGrandTotalAmount();
        $return[self::SOFORT_NODE_CURRENCY_CODE] = $payment->getOrder()->getCurrencyCode();


        return $return;
    }

}
