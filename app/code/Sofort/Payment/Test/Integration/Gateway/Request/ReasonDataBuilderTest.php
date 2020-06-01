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

class ReasonDataBuilderTest extends \PHPUnit_Framework_TestCase
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
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Config
     */
    protected $_resourceConfig;

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
            ->create('Sofort\Payment\Gateway\Request\ReasonDataBuilder');
        
        $this->_scopeConfig = Bootstrap::getObjectManager()->create(ScopeConfigInterface::class);

        $this->_resourceConfig = Bootstrap::getObjectManager()->create(Config::class);

        /*
         * Set default value for Reason1
         */
        $this->_resourceConfig->saveConfig(
            \Sofort\Payment\Helper\Config::SOFORT_REASONONE_KEY,
            '{{order_id}} {{customer_name}}',
            'default',
            0
        );
    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_dataBuilder instanceof BuilderInterface,
            get_class($this->_dataBuilder) . ' is present.'
        );
    }

    public function testStructure()
    {
        $this->markTestIncomplete('ReasonDataBuilderTest -> testStructure in development.');
        $result = $this->_dataBuilder
            ->build(['amount' => $this->_orderAdapter->getGrandTotalAmount(), 'payment' => $this->_paymentDataObject]);
        $this->assertTrue(is_array($result), 'Result is no array.');
        $this->assertCount(1, $result, count($result) . ' Elements returned.');
        foreach ($result as $key => $base) {
            $this->assertEquals('reasons', $key, $key . ' Wrong key present.');
            $this->assertTrue(
                count($base) >= 1 && count($base) <= 2,
                count($base) . ' Elements returned. There must be 1 or 2 Elements be returned.'
            );
        }
    }
}
