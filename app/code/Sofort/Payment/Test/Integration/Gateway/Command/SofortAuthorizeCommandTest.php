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

use Magento\Payment\Gateway\Command\GatewayCommand;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Helper\Reasons;
use Sofort\Payment\Gateway\Http\Client\SofortClient;
use Sofort\Payment\Gateway\Http\TransferFactory;
use Sofort\Payment\Gateway\Validator\BaseValidator;

class SofortAuthorizeCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Sofort\Payment\Helper\Tests
     */
    protected $_testsHelper;
    /**
     * @var GatewayCommand
     */
    protected $_command;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var OrderAdapterInterface
     */
    protected $_orderAdapter;

    /**
     * @var PaymentDataObject
     */
    protected $_paymentDataObject;

    protected function setUp()
    {
        $this->markTestIncomplete('WIP');
        $this->_testsHelper = Bootstrap::getObjectManager()->get(\Sofort\Payment\Helper\Tests::class);
        $this->_order = $this->_testsHelper->getOrder();
        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($this->_order);

        $params = [
            'requestBuilder' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('SofortInitiateRequest'),
            'transferFactory' => Bootstrap::getObjectManager()
                ->create(TransferFactory::class),
            'client' => Bootstrap::getObjectManager()
                ->create(SofortClient::class),
            'handler' => Bootstrap::getObjectManager()
                ->create('SofortInitiateHandler'),
            'validator' => Bootstrap::getObjectManager()
                ->create(BaseValidator::class),
        ];

        $this->_command = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('SofortInitiateCommand', $params);

        $order = $this->_testsHelper->getOrder();

        $this->_orderAdapter = $this->_testsHelper->getOrderAdapter($order);

        $this->_info = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Payment\Info::class);

        $this->_paymentDataObject = Bootstrap::getObjectManager()
            ->create(PaymentDataObject::class, ['order' => $this->_orderAdapter, 'payment' => $this->_info]);

        $this->_requestParams['payment'] = $this->_paymentDataObject;
        $this->_requestParams['amount'] = $this->_orderAdapter->getGrandTotalAmount();
    }

    public function testExecute()
    {
        $this->markTestIncomplete('WIP');
        $result = $this->_command->execute($this->_requestParams);
    }
}
