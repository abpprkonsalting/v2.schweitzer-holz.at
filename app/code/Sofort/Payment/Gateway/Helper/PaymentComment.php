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


use Magento\Framework\App\Helper\AbstractHelper;
use Sofort\Payment\Gateway\Exception\ResponseException;

class PaymentComment extends AbstractHelper
{
    /**
     * Log messages for events
     *
     * @var array
     */
    protected $_commentMessages = array(
        'redirect' =>
            'Redirection to SOFORT Banking. Transaction not finished. Transaction ID: [[transaction_id]]. Time: [[date]]',
        'abort' => 'Payment aborted. Time: %s',
        'pending_not_credited_yet' =>
            'SOFORT Banking has been completed successfully - Transaction ID: [[transaction_id]]. Time: [[date]]',
        'untraceable_sofort_bank_account_needed' =>
            'SOFORT Banking has been completed successfully - Transaction ID: [[transaction_id]]. Time: [[date]]',
        'loss_not_credited' =>
            'The payment has not been received on your SOFORT Bank account. Please verify the payment. Time: [[date]]',
        'received_credited' =>
            'The payment has been received on your SOFORT Bank account. Time: [[date]]',
        'received_partially_credited' =>
            'An amount differing from the order has been received on your SOFORT Bank account. Please verify the payment. Time: [[date]]',
        'received_overpayment' =>
            'An amount differing from the order has been received on your SOFORT Bank account. Please verify the payment. Time: [[date]]',
        'refunded_compensation' => 'Partial amount will be refunded - [[refunded_amount]]. Time: [[date]]',
        'refunded_refunded' => 'Amount will be refunded. Time: [[date]]'
    );

    /**
     * @param string $key
     * @return mixed|string
     */
    public function getComment($key = '')
    {
        $return = '';
        if (
            !empty($key)
            && array_key_exists($key, $this->_commentMessages)
        ) {
            $return = $this->_commentMessages[$key];
        }

        return $return;
    }

    /**
     * Get comment message with replaced placeholder
     *
     * @param $key
     * @param array keys: sofort_transaction_id
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getFilledComment($key, array $data)
    {
        $comment = __($this->getComment($key));

        if (isset($data['sofort_transaction_id'])) {
            $comment = str_replace('[[transaction_id]]', $data['sofort_transaction_id'], $comment);
        } elseif (isset($data['transactionId'])) {
            $comment = str_replace('[[transaction_id]]', $data['transactionId'], $comment);
        }
        $comment = str_replace('[[date]]', date('d.m.Y H:i:s'), $comment);
        $comment = str_replace(
            '[[refunded_amount]]',
            array_key_exists('amountRefunded', $data) ? $data['amountRefunded'] : 'Not available',
            $comment
        );

        return $comment;
    }
}
