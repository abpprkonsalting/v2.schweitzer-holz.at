<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Test\Integration\Gateway;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Helper\Reasons;

class ReasonsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Sofort\Payment\Helper\Tests
     */
    protected $_testsHelper;
    /**
     * @var Reasons
     */
    protected $_helper;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var OrderAdapterInterface
     */
    protected $_orderAdapter;

    protected function setUp()
    {
        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);
        $this->_order = $this->_testsHelper->getOrder();
        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($this->_order);
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Sofort\Payment\Gateway\Helper\Reasons::class);
    }

    public function testMapValues()
    {
//        print_r($this->_order->getBillingAddress()->getLastname());die();
        $text = [
            '{{order_id}}',
            '{{date}}',
            '{{customer_name}}',
            '{{customer_company}}',
            '{{transaction_id}}',
            '{{shopname}}',
            '{{name}}',
            '{{customer_email}}'
        ];

        $results = [
            $this->_order->getIncrementId(),
            date('d.m.Y', time()),
            $this->_order->getBillingAddress()->getFirstname()
            . ' '
            . $this->_order->getBillingAddress()->getLastname(),
            $this->_order->getBillingAddress()->getCompany(),
            '-TRANSACTION-',
            '',
            $this->_order->getBillingAddress()->getFirstname()
            . ' '
            . $this->_order->getBillingAddress()->getLastname(),
            $this->_order->getBillingAddress()->getEmail()
        ];

        foreach ($text as $key => $part) {
            $this->assertEquals(
                $results[$key],
                $this->_helper->mapValues($this->_orderAdapter, $part),
                $key . ' ' . $part
            );
        }
    }
}
