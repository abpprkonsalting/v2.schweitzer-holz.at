<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\Client\Zend;
use Magento\Payment\Gateway\Http\ClientInterface;
use Sofort\Payment\Gateway\Logger\SofortLoggerInterface;
use Sofort\Payment\Helper\Config;
use Sofort\Payment\Helper\Xml2Array;

/**
 * Class SofortClient
 * @package Sofort\Payment\Gateway\Http\Client
 */
class SofortClient implements ClientInterface
{
    /**
     * @var Zend
     */
    protected $_paymentZendCurlClient;

    /**
     * @var Xml2Array
     */
    protected $_xml2Array;

    /**
     * @var SofortLoggerInterface
     */
    protected $_sofortLogger;

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * SofortClient constructor.
     * @param Zend $paymentSoapClient
     * @param Xml2Array $xml2Array
     */
    public function __construct(
        Zend $paymentSoapClient,
        Xml2Array $xml2Array,
        SofortLoggerInterface $sofortLoggerInterface,
        Config $configHelper
    ) {
        $this->_paymentZendCurlClient = $paymentSoapClient;
        $this->_xml2Array = $xml2Array;
        $this->_sofortLogger = $sofortLoggerInterface;
        $this->_configHelper = $configHelper;
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return \Sofort\Payment\Helper\DOMDocument
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        if ($this->_configHelper->getLoggingActive()) {
            $this->_sofortLogger->info($transferObject->getBody());
        }

        $response = $this->_paymentZendCurlClient->placeRequest($transferObject);

        if ($this->_configHelper->getLoggingActive()) {
            $this->_sofortLogger->info($response);
        }

        $return = $this->_xml2Array->createArray($response[0]);

        return $return;
    }
}
