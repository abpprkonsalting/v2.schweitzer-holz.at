<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\payment\Test\Integration\Gateway\Request;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Data\Rewrite\Order\OrderAdapter;
use Sofort\Payment\Helper\Array2Xml;

class Array2XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Array2Xml
     */
    protected $_helper;

    /**
     * @var Sofort\Payment\Helper\Tests
     */
    protected $_testsHelper;

    /**
     * @var SofortAuthorizeRequest
     */
    protected $_authorizeRequestBuilder;

    /**
     * @var array
     */
    protected $_result = [];

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    protected function setUp()
    {
        /**
         * @var \Magento\Config\Model\ResourceModel\Config $scopeConfig
         */
        $this->config = Bootstrap::getObjectManager()->get(\Magento\Config\Model\ResourceModel\Config::class);
        $this->config->beginTransaction();
        $this->config->saveConfig(
            'general/store_information/name',
            'Hallö',
            'default',
            0
        );
        $this->config->saveConfig(
            'payment/sofortueberweisung/reason1',
            '{{shopname}}',
            'default',
            0
        );
        $this->config->commit();

        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(Array2Xml::class);
        $this->_authorizeRequestBuilder =
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('SofortInitiateRequest');

        $order = $this->_testsHelper->getOrder();

        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($order);

        $this->_info = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Payment\Info::class);

        $this->_paymentDataObject = Bootstrap::getObjectManager()
            ->create(PaymentDataObject::class, ['order' => $this->_orderAdapter, 'payment' => $this->_info]);

        $this->_requestParams['payment'] = $this->_paymentDataObject;
        $this->_requestParams['amount'] = $this->_orderAdapter->getGrandTotalAmount();

        $this->_result = $this->_authorizeRequestBuilder->build($this->_requestParams);
    }

    /**
     * Helper instance test
     */
    public function testHelperInstance()
    {
        $this->assertTrue($this->_helper instanceof AbstractHelper, get_class($this->_helper) . ' is present.');
    }

    public function testArray2Xml()
    {
        /**
         * @var \DomDocument $result
         */
        $result = $this->_helper->createXML('multipay', $this->_result);
        $resultString = $result->saveXML();

        $this->assertTrue($result instanceof \DOMDocument, 'No DomDocument present');
        $this->assertEquals('multipay', $result->firstChild->tagName, 'multipay Node not present.');
        $this->assertContains('multipay', $resultString, 'multipay is not present in result string');
        $this->assertContains('project_id', $resultString, 'project_id is not present in result string');
        $this->assertContains('reasons', $resultString, 'reasons is not present in result string');
        $this->assertContains('reason', $resultString, 'reason is not present in result string');
    }
}
