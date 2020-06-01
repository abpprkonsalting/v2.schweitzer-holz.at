<?php

namespace Sofort\Payment\Model\Backend\Options\Order;

/**
 * Class Invoice
 * @package Sofort\Payment\Model\Backend\Options\Order
 */
class Invoice implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Define which Creditcard Logos are shown for payment
     *
     * @return array
     */
    public function toOptionArray()
    {
        $presentationMethods = array(
            array(
                'label' => __('After finishing the payment'),
                'value' => 'after_order'
            ),
            array(
                'label' => __('The receipt of the money ( can be only detected with an account with SOFORT Bank)'),
                'value' => 'after_credited'
            ),
            array(
                'label' => __('Not create invoice'),
                'value' => 'no_invoice'
            ),
        );
        
        return $presentationMethods;
    }
}
