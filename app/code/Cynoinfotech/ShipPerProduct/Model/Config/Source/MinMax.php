<?php
/**
 * @author Cynoinfotech Team
 * @package Cynoinfotech_ShipPerProduct
 */
namespace Cynoinfotech\ShipPerProduct\Model\Config\Source;

class MinMax implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'min', 'label' => __('Min')],
            ['value' => 'max', 'label' => __('Max')],
            
        ];
    }
}
