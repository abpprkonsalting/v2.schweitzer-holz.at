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


use Magento\Catalog\Model\Product;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Data\Rewrite\Order\OrderAdapter;
use Sofort\Payment\Gateway\Request\OrderDataBuilder;

class OrderDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_dataBuilder;

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
        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);

        $order = $this->_testsHelper->getOrder();

        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($order);

        $this->_info = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Payment\Info::class);

        $this->_paymentDataObject = Bootstrap::getObjectManager()
            ->create(PaymentDataObject::class, ['order' => $this->_orderAdapter, 'payment' => $this->_info]);

        $this->_dataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(OrderDataBuilder::class);

    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_dataBuilder instanceof BuilderInterface,
            get_class($this->_dataBuilder) . ' is present.'
        );
    }

    public function testBuildData()
    {
        $result = $this->_dataBuilder
            ->build(['amount' => $this->_orderAdapter->getGrandTotalAmount(), 'payment' => $this->_paymentDataObject]);
        $this->assertArrayHasKey('amount', $result, 'amount Key is not present');
        $this->assertArrayHasKey('currency_code', $result, 'amount Key is not present');

        $this->assertEquals(
            $this->_orderAdapter->getGrandTotalAmount(),
            $result['amount'],
            'Wrong amount present. Return: ' . $result['amount']
        );
        $this->assertEquals(
            $this->_orderAdapter->getCurrencyCode(),
            $result['currency_code'],
            'Wrong currency present. Returned: ' . $result['currency_code']
        );
    }
}
