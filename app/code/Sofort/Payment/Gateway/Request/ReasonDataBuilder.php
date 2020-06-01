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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Sofort\Payment\Gateway\Helper\Reasons;
use Sofort\Payment\Helper\Config;

/**
 * Class ReasonDataBuilder
 * @package Sofort\Payment\Gateway\Request
 */
class ReasonDataBuilder implements BuilderInterface
{
    /**
     * Const for main XML node
     */
    const REASON_MAIN = 'reasons';

    /**
     * Const for sub node
     */
    const REASON_ITEM = 'reason';

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Reasons
     */
    protected $_reasonsHelper;

    /**
     * ReasonDataBuilder constructor.
     * @param Config $configHelper
     * @param Reasons $reasonsHelper
     */
    public function __construct(
        Config $configHelper,
        Reasons $reasonsHelper
    )
    {
        $this->_configHelper = $configHelper;
        $this->_reasonsHelper = $reasonsHelper;
    }

    /**
     * Generate base data
     *
     * @param array $buildSubject
     */
    public function build(array $buildSubject)
    {
        /**
         * @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject
         */
        $paymentDataObject = $buildSubject['payment'];

        /**
         * @var \Magento\Payment\Gateway\Data\Order\OrderAdapter $order
         */
        $order = $paymentDataObject->getOrder();


        $return[self::REASON_MAIN] = [];

        /*
         * Pattern key
         */
        $reasonOne = $this->_configHelper->getReasonOne();
        $reasonTwo = $this->_configHelper->getReasonTwo();

        $text = $this->_reasonsHelper->mapValues($order, $reasonOne);
        $text2 = $this->_reasonsHelper->mapValues($order, $reasonTwo);

        $default = $this->_reasonsHelper->addDefaultValue($text, $text2);

        if (!empty($default)) {
            $text = $default;
        }

        $return[self::REASON_MAIN][self::REASON_ITEM][] = $text;
        $return[self::REASON_MAIN][self::REASON_ITEM][] = $text2;

        return $return;
    }

    /**
     * @param $reason
     * @param string $pattern
     * @param int $reasonLength
     * @return mixed|string
     */
    protected function _cutReason($reason, $pattern = '#[^a-zA-Z0-9+-\.,]#', $reasonLength = 27)
    {
        $reason = preg_replace($pattern, ' ', $reason);
        $reason = substr($reason, 0, $reasonLength);
        return $reason;
    }
}
