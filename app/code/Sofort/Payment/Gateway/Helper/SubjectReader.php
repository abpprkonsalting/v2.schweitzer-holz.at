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
 * Class SubjectReader
 * @package Sofort\Payment\Gateway\Helper
 */
class SubjectReader
{
    /**
     * Reads response object from subject
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        $response = Helper\SubjectReader::readResponse($subject);

        return $response['object'];
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * @param array $subject
     * @return mixed
     */
    public function readStateObject(array $subject)
    {
        $result = SubjectReader::readStateObject($subject);

        return $result;
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads customer id from subject
     *
     * @param array $subject
     * @return int
     */
    public function readCustomerId(array $subject)
    {
        if (!isset($subject['customer_id'])) {
            throw new \InvalidArgumentException('The "customerId" field does not exists');
        }

        return (int) $subject['customer_id'];
    }

    /**
     * Reads public hash from subject
     *
     * @param array $subject
     * @return string
     */
    public function readPublicHash(array $subject)
    {
        if (empty($subject[PaymentTokenInterface::PUBLIC_HASH])) {
            throw new \InvalidArgumentException('The "public_hash" field does not exists');
        }

        return $subject[PaymentTokenInterface::PUBLIC_HASH];
    }

    /**
     * Reads PayPal details from transaction object
     *
     * @param Transaction $transaction
     * @return array
     */
    public function readPayPal(Transaction $transaction)
    {
        if (!isset($transaction->paypal)) {
            throw new \InvalidArgumentException('Transaction has\'t paypal attribute');
        }

        return $transaction->paypal;
    }
}
