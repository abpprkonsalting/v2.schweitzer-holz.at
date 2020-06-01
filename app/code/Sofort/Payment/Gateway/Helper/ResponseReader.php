<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sofort\Payment\Gateway\Helper;

use Braintree\Transaction;
use Magento\Quote\Model\Quote;
use Magento\Payment\Gateway\Helper;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Class ResponseReader
 * @package Sofort\Payment\Gateway\Helper
 */
class ResponseReader
{
    /**
     * @param array $response
     * @return string
     */
    public function readTransaction(array $response)
    {
        if (
            isset($response['new_transaction'])
            && isset($response['new_transaction']['transaction'])
            && !empty($response['new_transaction']['transaction'])
        ) {
            return $response['new_transaction']['transaction'];
        } else {
            return '0';
        }
    }

    /**
     * @param array $response
     * @return string
     */
    public function readPaymentUrl(array $response)
    {
        if (
            isset($response['new_transaction'])
            && isset($response['new_transaction']['payment_url'])
            && !empty($response['new_transaction']['payment_url'])
        ) {
            return $response['new_transaction']['payment_url'];
        } else {
            return '';
        }
    }

    /**
     * @param array $response
     * @return mixed
     */
    public function readTransactionStatus(array $response)
    {
        if (
            isset($response['transactions'])
            && isset($response['transactions']['transaction_details'])
            && isset($response['transactions']['transaction_details']['status'])
        ) {
            return $response['transactions']['transaction_details']['status'];
        }

        return '';
    }

    /**
     * @param array $response
     * @return mixed
     */
    public function readTransactionStatusComment(array $response)
    {
        if (
            isset($response['transactions'])
            && isset($response['transactions']['transaction_details'])
            && isset($response['transactions']['transaction_details']['status_reason'])
        ) {
            return $response['transactions']['transaction_details']['status_reason'];
        }

        return '';
    }

    /**
     * @param array $response
     * @return string
     */
    public function readAmountRefunded(array $response)
    {
        if (
            isset($response['transactions'])
            && isset($response['transactions']['transaction_details'])
            && isset($response['transactions']['transaction_details']['amount_refunded'])
        ) {
            return $response['transactions']['transaction_details']['amount_refunded'];
        }

        return '';
    }
}
