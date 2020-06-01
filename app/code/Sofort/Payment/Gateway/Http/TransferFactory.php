<?php

/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Http;

use Magento\Payment\Gateway\Http\Client\Zend;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use Sofort\Payment\Helper\Array2Xml;
use Sofort\Payment\Helper\Config;
use Sofort\Payment\Helper\Version;

/**
 * Class TransferFactory
 * @package Sofort\Payment\Gateway\Http
 */
class TransferFactory implements \Magento\Payment\Gateway\Http\TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    protected $_transferBuilder;

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Array2Xml
     */
    protected $_array2XmlHelper;

    /**
     * @var \Sofort\Payment\Gateway\Config\Config
     */
    protected $_paymentHelperVersion;

    /**
     * TransferFactory constructor.
     * @param TransferBuilder $transferBuilder
     * @param Config $config
     * @param Array2Xml $array2Xml
     * @param Version $paymentHelperConfig
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        Config $config,
        Array2Xml $array2Xml,
        Version $paymentHelperConfig
    ) {
        $this->_transferBuilder = $transferBuilder;
        $this->_configHelper = $config;
        $this->_array2XmlHelper = $array2Xml;
        $this->_paymentHelperVersion = $paymentHelperConfig;
    }

    /**
     * Create transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        /*
         * transform request array to xml
         */
        $requestXmlData = $this->_array2XmlHelper->createXML('multipay', $request);
        $requestXml = $requestXmlData->saveXml();

        return $this->_transferBuilder
            ->setMethod(\Zend_Http_Client::POST)
            ->setUri($this->_configHelper->getApiUri())
            ->setHeaders(
                [
                    'User-Agent: SofortLib-php/'.$this->_paymentHelperVersion->getModuleSignature().'-'.\Zend_Http_Client::POST,
                    'Content-Type: application/xml; charset=UTF-8',
                    'Accept: application/xml; charset=UTF-8',
                    'Authorization: Basic '.base64_encode($this->_configHelper->getCustomerId().':'.$this->_configHelper->getApiKey())
                ]
            )
            ->setAuthUsername($this->_configHelper->getCustomerId())
            ->setAuthPassword($this->_configHelper->getApiKey())
            ->setBody(['xml' => $requestXml])
            ->build();
    }

}
