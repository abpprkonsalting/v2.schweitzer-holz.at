<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Model\Backend\Options;

/**
 * Class PresentationCheckout
 * @package Sofort\Payment\Model\Backend\Options
 */
class PresentationCheckout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('Banner')
            ],
            [
                'value' => '1',
                'label' => __('Logo & Text')
            ]
        ];
    }

}
