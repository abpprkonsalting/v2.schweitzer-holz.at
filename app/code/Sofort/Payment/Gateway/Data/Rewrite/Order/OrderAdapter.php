<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Data\Rewrite\Order;

use Magento\Payment\Gateway\Data\Order\AddressAdapterFactory;
use Magento\Sales\Model\Order;

/**
 * Class OrderAdapter
 * @package Sofort\Payment\Gateway\Data\Rewrite\Order
 */
class OrderAdapter extends \Magento\Payment\Gateway\Data\Order\OrderAdapter
{
    /**
     * @var Order
     */
    protected $_order;

    /**
     * OrderAdapter constructor.
     * @param Order $order
     * @param AddressAdapterFactory $addressAdapterFactory
     */
    public function __construct(
        Order $order,
        AddressAdapterFactory $addressAdapterFactory
    ) {
        $this->_order = $order;

        parent::__construct($order, $addressAdapterFactory);
    }

    /**
     * @return null|string
     */
    public function getOrderCreatedAt()
    {
        return $this->_order->getCreatedAt();
    }

    public function setCustomerNoteNotify($value)
    {
        $this->_order->setCustomerNoteNotify($value);
    }
}
