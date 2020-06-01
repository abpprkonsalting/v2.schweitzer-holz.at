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
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Data\Rewrite\Order\OrderAdapter;

class SofortAuthorizedRequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_authorizeRequestBuilder;

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
     * @var array
     */
    protected $_requestParams = [
        'payment' => ''
    ];

    protected function setUp()
    {
        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);

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
    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_authorizeRequestBuilder instanceof \Magento\Payment\Gateway\Request\BuilderComposite,
            get_class($this->_authorizeRequestBuilder) . ' is present.'
        );
    }

    public function testAuthorizedRequest()
    {
        $result = $this->_authorizeRequestBuilder->build($this->_requestParams);
        $this->assertTrue(is_array($result), 'No array returned.');
        $this->assertArrayHasKey('project_id', $result, 'No project_id present in result.');
        $this->assertArrayHasKey('interface_version', $result, 'No interface_version present in result.');
    }
}
