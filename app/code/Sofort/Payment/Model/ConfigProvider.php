<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sofort\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Sofort\Payment\Helper\Data;

/**
 * Class ConfigProvider
 * @package Sofort\Payment\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $dataHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $test = $this->_scopeConfig
            ->getValue('payment/sofortueberweisung/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (
            !$this->_scopeConfig
                ->getValue('payment/sofortueberweisung/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ) {
            return [];
        }

        // Recommended payment
        $recompaymentString = '';
        $recomPayment = $this->_scopeConfig->getValue(
            'payment/sofortueberweisung/recommendedPayment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($recomPayment) {
            $recompaymentString = __('(recommended payment methode)');
        }

        return [
            'payment' => [
                'sofort' => [
                    'isBanner' => $this->_scopeConfig->getValue(
                        'payment/sofortueberweisung/checkoutPresentation',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ) == 0,
                    'recommendedPayment' => $recompaymentString
                ],
                'active' => $this->_scopeConfig->getValue(
                    'payment/sofortueberweisung/active',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            ]
        ];
    }
}
