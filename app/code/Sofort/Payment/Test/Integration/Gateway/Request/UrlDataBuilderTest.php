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


use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Request\OrderDataBuilder;
use Sofort\Payment\Gateway\Request\UrlDataBuilder;

class UrlDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_baseDataBuilder;


    /**
     * @var \Magento\Payment\Gateway\Data\Order\OrderAdapter
     */
    protected $_orderAdapter;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    protected $_info;

    /**
     * @var Sofort\Payment\Helper\Tests
     */
    protected $_testsHelper;

    /**
     * @var PaymentDataObject
     */
    protected $_paymentDataObject;

    /**
     * @var array
     */
    protected $_requestData = [
        'payment'
    ];

    protected function setUp()
    {
        $this->_baseDataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(UrlDataBuilder::class);

        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);

        $order = $this->_testsHelper->getOrder();

        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($order);

        $this->_info = Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Payment\Info::class);

        $this->_paymentDataObject = Bootstrap::getObjectManager()
            ->create(PaymentDataObject::class, ['order' => $this->_orderAdapter, 'payment' => $this->_info]);

        $this->_dataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(OrderDataBuilder::class);
    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_baseDataBuilder instanceof BuilderInterface,
            get_class($this->_baseDataBuilder) . ' is present.'
        );
    }

    public function testStructure()
    {
        $result = $this->_baseDataBuilder->build(
            [
                'payment' => $this->_paymentDataObject
            ]
        );
        $this->assertTrue(is_array($result), 'Result is no array.');
        $this->assertArrayHasKey('success_url', $result, 'success_url is not present');
        $this->assertArrayHasKey('success_link_redirect', $result, 'success_link_redirect is not present');
        $this->assertArrayHasKey('abort_url', $result, 'abort_url is not present');
        $this->assertArrayHasKey('notification_urls', $result, 'notification_urls is not present');
        $this->assertArrayHasKey('notification_url', $result['notification_urls'], 'notification_url is not present');
    }
}
