<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Helper;


use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 * @package Sofort\Payment\Helper
 */
class Config extends AbstractHelper
{
    /**
     * Config key path
     */
    const SOFORT_CONFIGURATION_KEY = 'payment/sofortueberweisung/configuration_key';

    /**
     *
     */
    const SOFORT_ACTIVE_KEY = 'payment/sofortueberweisung/active';

    /**
     *
     */
    const SOFORT_TITLE_KEY = 'payment/sofortueberweisung/title';

    /**
     *
     */
    const SOFORT_REASONONE_KEY = 'payment/sofortueberweisung/reason1';

    const SOFORT_REASONTWO_KEY = 'payment/sofortueberweisung/reason2';

    /**
     *
     */
    const SOFORT_API_KEY = 'payment/sofortueberweisung/apiUri';

    /**
     *
     */
    const SOFORT_LOGGING = 'payment/sofortueberweisung/logging';

    const SOFORT_SEND_ORDER_CONFIRMATION = 'payment/sofortueberweisung/send_order_confirmation';

    /**
     * @var String
     */
    protected $_configurationKey;

    /**
     * @var String
     */
    protected $_customerId;

    /**
     * @var String
     */
    protected $_productId;

    /**
     * @var String
     */
    protected $_apiKey;

    /**
     * Get SOFORT product key from configuration key
     *
     * @return String
     */
    public function getProjectKey()
    {
        $this->_loadConfigurationKey()
            ->_splitConfigKey();

        return $this->_productId;
    }

    /**
     * Get SOFORT customer id
     *
     * @return String
     */
    public function getCustomerId()
    {
        $this->_loadConfigurationKey()
            ->_splitConfigKey();

        return $this->_customerId;
    }

    /**
     * Get SOFORT api key
     *
     * @return String
     */
    public function getApiKey()
    {
        $this->_loadConfigurationKey()
            ->_splitConfigKey();

        return $this->_apiKey;
    }

    /**
     * Load sofort configuration key
     *
     * return Config
     */
    protected function _loadConfigurationKey()
    {
        $this->_configurationKey = $this->scopeConfig->getValue(self::SOFORT_CONFIGURATION_KEY);

        return $this;
    }

    /**
     * Split config key
     *
     * @return Config
     */
    protected function _splitConfigKey()
    {
        if (empty($this->_configurationKey)
            || empty($this->_customerId)
            || empty($this->_productId)
            || empty($this->_apiKey)
        ) {
            $temp = explode(':', $this->_configurationKey);

            if (count($temp) == 3) {
                $this->_customerId = $temp[0];
                $this->_productId = $temp[1];
                $this->_apiKey = $temp[2];
            } else {
                // Key is not correct
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReasonOne()
    {
        return $this->scopeConfig->getValue(self::SOFORT_REASONONE_KEY);
    }

    public function getReasonTwo()
    {
        return $this->scopeConfig->getValue(self::SOFORT_REASONTWO_KEY);
    }

    /**
     * @return mixed
     */
    public function getApiUri()
    {
        return $this->scopeConfig->getValue(self::SOFORT_API_KEY);
    }

    /**
     * @return mixed
     */
    public function getLoggingActive()
    {
        return $this->scopeConfig->getValue(self::SOFORT_LOGGING);
    }

    public function getSendOrderConfirmation()
    {
        return $this->scopeConfig->getValue(self::SOFORT_SEND_ORDER_CONFIRMATION);
    }
}
