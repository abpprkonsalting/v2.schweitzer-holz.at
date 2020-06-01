<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Config;

/**
 * Class Config
 * @package Sofort\Payment\Gateway\Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    /**
     * Check if payment is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(\Sofort\Payment\Helper\Config::SOFORT_ACTIVE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($field, $storeId = null)
    {
        if ($field == 'title') {
            return __(parent::getValue($field, $storeId));
        } elseif ($field == 'order_place_redirect_url') {
            // immer true, damit bei place order keine mail versendet wird; im pre step den flag auswerten
            return true;
            return (boolean)parent::getValue('send_order_confirmation', $storeId);
        } elseif ($field == 'sort_order') {
            $value = parent::getValue($field, $storeId);
            if ($value == 0 || $value == 1) {
                return -1;
            } else {
                return $value;
            }
        }
        return parent::getValue($field, $storeId);
    }


    /**
     * Get payment title
     * @return string
     */
    public function getTitle()
    {
        $title = parent::getValue(\Sofort\Payment\Helper\Config::SOFORT_TITLE_KEY);
        return parent::getValue($title);
    }

    /**
     * @return boolean
     */
    public function getSendOrderConfirmation()
    {
        return (boolean) parent::getValue(
            'send_order_confirmation',
            null
        );
    }

    /**
     * @return boolean
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getSendOrderEmail();
    }
}
