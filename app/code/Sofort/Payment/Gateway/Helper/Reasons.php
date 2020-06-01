<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Helper;


use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;

/**
 * Class Reasons
 * @package Sofort\Payment\Gateway\Helper
 */
class Reasons
{
    const SOFORT_TRANSACTION_BASE = '{{transaction_id}}';

    const SOFORT_TRANSACTION_KEY = '-TRANSACTION-';

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * Mapping definition for placeholders
     *
     * @var array
     */
    protected $_map = [
        '{{order_id}}' => 'getOrderIncrementId',
        '{{customer_id}}' => 'getCustomerId',
        '{{customer_email}}' => ['getShippingAddress' => ['getEmail']],
        '{{customer_name}}' => ['getShippingAddress' => ['getFirstname', 'getLastName']],
        '{{name}}' => ['getShippingAddress' => ['getFirstname', 'getLastName']],
        '{{customer_company}}' => ['getShippingAddress' => ['getCompany']],
        self::SOFORT_TRANSACTION_BASE => ['const' => self::SOFORT_TRANSACTION_KEY],
        '{{transaction}}' => ['const' => self::SOFORT_TRANSACTION_KEY],
    ];

    /**
     * Reasons constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CustomerRepository $customerRepository
    ){
        $this->_map['{{shopname}}'] = ['const' => $scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )];
        $this->_map['{{date}}'] = ['const' => date('d.m.Y', time())];
        $this->_customerRepository = $customerRepository;
    }

    /**
     * Map values
     *
     * @param OrderAdapterInterface $order
     * @param string $text
     * @return mixed|string
     */
    public function mapValues(OrderAdapterInterface $order, $text = '')
    {
        $return = $text;

        /*
         * Check for transaction_id
         * => if not present then add the transaction key
         */
//        $addTransactionKey = true;
//        if (strpos($text, self::SOFORT_TRANSACTION_BASE) !== false) {
//            $addTransactionKey = false;
//        }

        foreach ($this->_map as $key => $value) {
            if (empty($value)) {
                return $return;
            }

            $elemt = '';
            if (is_array($value)) {
                $elemt = $this->_getArrayValues($order, $value);
            } else {
                $elemt = $order->$value();
            }
            if (strpos($text, $key) !== null) {
                $return = str_replace($key, $elemt, $return);
            }

        }

//        if (strpos($text, '{{date}}') !== false) {
//            $return = str_replace('{{date}}', date('d.m.Y H:i:s', time()), $return);
//        }

//        if ($addTransactionKey) {
//            $return .= ' ' . self::SOFORT_TRANSACTION_KEY;
//        }

        return $return;
    }

    /**
     * @param OrderAdapterInterface $order
     * @param array $values
     * @return mixed|string
     */
    protected function _getArrayValues(OrderAdapterInterface $order, array $values)
    {
        $return = '';
        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                if ($key == 'const') {
                    return $value;
                }
                break;
            }
            $temp = $order->$key();
            $temp2 = [];
            foreach ($value as $item) {
                $temp2[] = $temp->$item();
            }
            $return = implode(' ', $temp2);
        }

        return $return;
    }

    /**
     * @param $reason1
     * @param $reason2
     * @return string
     */
    public function addDefaultValue($reason1, $reason2)
    {
        $return = '';
        if (empty($reason1) && empty($reason2)) {
            $return = self::SOFORT_TRANSACTION_KEY;
        }

        return $return;
    }
}
