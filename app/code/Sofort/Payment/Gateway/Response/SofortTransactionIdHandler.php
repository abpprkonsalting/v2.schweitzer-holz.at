<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sofort\Payment\Gateway\Response;

use Magento\Authorizenet\Controller\Directpost\Payment\Response;
use Magento\Customer\Model\Session;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Sofort\Payment\Gateway\Exception\ResponseException;
use Sofort\Payment\Gateway\Helper\Error;
use Sofort\Payment\Gateway\Helper\ResponseReader;
use Sofort\Payment\Gateway\Helper\SubjectReader;

/**
 * Class SofortTransactionIdHandler
 * @package Sofort\Payment\Gateway\Response
 */
class SofortTransactionIdHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Error
     */
    protected $_errorHelper;

    /**
     * @var ResponseReader
     */
    protected $_responseReader;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * SofortTransactionIdHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Error $errorHelper
     * @param ResponseReader $responseReader
     * @param Session $customerSession
     */
    public function __construct(
        SubjectReader $subjectReader,
        Error $errorHelper,
        ResponseReader $responseReader,
        Session $customerSession
    ) {
        $this->subjectReader = $subjectReader;
        $this->_errorHelper = $errorHelper;
        $this->_responseReader = $responseReader;
        $this->_customerSession = $customerSession;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        /**
         * @var PaymentDataObjectInterface $paymentData
         */
        $paymentData = $this->subjectReader->readPayment($handlingSubject);

        $errorResult = $this->_errorHelper->validateResponse($response);
        if (empty($errorResult)) {
            $transactionId = $this->_responseReader->readTransaction($response);
            if (!empty($transactionId)) {
                $paymentData->getPayment()->setAdditionalInformation('sofort_transaction_id', $transactionId);
                $paymentData->getPayment()->setAdditionalInformation('redirect_url', $this->_responseReader->readPaymentUrl($response));

                $paymentData->getPayment()->setTransactionId($transactionId);
                $paymentData->getPayment()->addTransaction('authorization');

                $paymentData->getPayment()->setTransactionAdditionalInfo(
                    'redirect_url',
                    $this->_responseReader->readPaymentUrl($response)
                );
                $this->_customerSession->setPaymentUrl(
                    $this->_responseReader->readPaymentUrl($response)
                );
            }
        }
    }

    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return false;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment)
    {
        return false;
    }
}
