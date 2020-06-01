<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Model\Backend\Options\Order;

/**
 * Class Status
 * @package Sofort\Payment\Model\Backend\Options\Order
 */
class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    protected $_sortOrderStatusOptions = [
        '',
        'canceled',
        'fraud',
        'holded',
        'payment_review',
        'pending',
        'pending_payment',
        'processing'
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionsToBeUnset = array(
            'pending_paypal',
            'paypal_canceled_reversal',
            'paypal_reversed',
            'complete',
            'unchanged',
            'closed',
        );
        
        $optionToAdd = [
            \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING_PAYMENT => 'Pending Payment',
            \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PAYMENT_REVIEW => 'Payment Review',
        ];

        $options = parent::toOptionArray();
        foreach ($options as $key => $option) {
            if (empty($option['value'])) {
                $options[$key]['label'] = __('Not update status');
            }

            if (in_array($option['value'], $optionsToBeUnset, true)) {
                unset($options[$key]);
            }
        }

        foreach ($optionToAdd as $key => $value) {
            $temp['value'] = $key;
            $temp['label'] = $value;

            $options[] = $temp;
        }

        $options = $this->_sortOptions($options);

        return $options;
    }

    /**
     * @param $options
     * @return array
     */
    protected function _sortOptions($options)
    {
        $sortedOptions = [];

        foreach ($this->_sortOrderStatusOptions as $sortOrderStatusOption) {
            foreach ($options as $option) {
                if ($sortOrderStatusOption == $option['value']) {
                    $sortedOptions[] = $option;
                    break;
                }
            }
        }

        return $sortedOptions;
    }
}
