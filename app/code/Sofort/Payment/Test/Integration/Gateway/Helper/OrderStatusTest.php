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

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Config\Scope;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Helper\OrderStatus;
use Sofort\Payment\Gateway\Helper\Reasons;

class OrderStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrderStatus
     */
    protected $_helper;

    /**
     * @var ConfigInterface
     */
    protected $_configResource;

    protected $_apiStatus = [
        'loss' => 'not_credited',
        'pending' => 'not_credited_yet',
        'received' => 'credited',
        'refunded' => 'refunded',
        'untraceable' => 'sofort_bank_account_needed'
    ];

    protected $_statusTransfers = [
        'loss_not_credited' => 'pendig',
        'pending_not_credited_yet' => 'pending',
        'received_credited' => 'pending',
        'refunded_refunded' => 'pending',
        'untraceable_sofort_bank_account_needed' => 'pending'
    ];

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Sofort\Payment\Gateway\Helper\OrderStatus::class);
        $this->_configResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(ConfigInterface::class);

        /*
         * Add config data for order status transfer (testTransferStatus)
         */
        foreach ($this->_statusTransfers as $path => $magentoStatus) {
            $this->_configResource
                ->saveConfig(OrderStatus::SOFORT_STATUS_CONFIG_BASE . $path, $magentoStatus, 'default', 0);
        }
    }

    public function testTransferStatus()
    {
        foreach ($this->_apiStatus as $status => $statusComment) {
            $data['status'] = $status;
            $data['statusComment'] = $statusComment;

            $this->assertEquals(
                $this->_statusTransfers[implode('_', $data)],
                $this->_helper->transferStatus($data),
                $status . ' ' . $statusComment . ' failed'
            );
        }
    }
}
