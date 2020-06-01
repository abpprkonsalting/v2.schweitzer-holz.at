<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sofort\Payment\Gateway\Response;

use Magento\Authorizenet\Controller\Directpost\Payment\Response;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Status\Collection;
//use MEQP2\Tests\NamingConventions\true\bool;
use Sofort\Payment\Gateway\Config\Config;
use Sofort\Payment\Gateway\Exception\ResponseException;
use Sofort\Payment\Gateway\Helper\Error;
use Sofort\Payment\Gateway\Helper\OrderStatus;
use Sofort\Payment\Gateway\Helper\PaymentComment;
use Sofort\Payment\Gateway\Helper\ResponseReader;
use Sofort\Payment\Gateway\Helper\SubjectReader;

/**
 * Class SofortTransactionDataHandler
 * @package Sofort\Payment\Gateway\Response
 */
class SofortTransactionDataHandler implements HandlerInterface
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
     * @var OrderStatus
     */
    protected $_orderStatusHelper;

    /**
     * @var Collection
     */
    protected $_statusCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $_orderResourceModel;

    /**
     * @var PaymentComment
     */
    protected $_paymentCommentHelper;

    /**
     * @var Order\Email\Sender\InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Order\CreditmemoFactory
     */
    protected $_creditMemoFactory;

    /**
     * @var CreditmemoManagementInterface
     */
    protected $_creditMemoManagement;

    /**
     * @var Order\Email\Sender\CreditmemoSender
     */
    protected $_creditMemoSender;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Order\Email\Sender\OrderSender
     */
    protected $_orderSender;

    /**
     * SofortTransactionDataHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Error $errorHelper
     * @param ResponseReader $responseReader
     * @param Session $customerSession
     * @param OrderStatus $orderStatusHelper
     * @param Collection $statusCollection
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param PaymentComment $paymentCommentHelper
     * @param Order\Email\Sender\InvoiceSender $invoiceSender
     * @param ScopeConfigInterface $scopeConfig
     * @param Order\CreditmemoFactory $creditmemoFactory
     * @param CreditmemoManagementInterface $creditmemoManagement
     * @param Order\Email\Sender\CreditmemoSender $creditmemoSender
     */
    public function __construct(
        SubjectReader $subjectReader,
        Error $errorHelper,
        ResponseReader $responseReader,
        Session $customerSession,
        OrderStatus $orderStatusHelper,
        Collection $statusCollection,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        PaymentComment $paymentCommentHelper,
        Order\Email\Sender\InvoiceSender $invoiceSender,
        ScopeConfigInterface $scopeConfig,
        Order\CreditmemoFactory $creditmemoFactory,
        CreditmemoManagementInterface $creditmemoManagement,
        Order\Email\Sender\CreditmemoSender $creditmemoSender,
        Config $config,
        Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->subjectReader = $subjectReader;
        $this->_errorHelper = $errorHelper;
        $this->_responseReader = $responseReader;
        $this->_customerSession = $customerSession;
        $this->_orderStatusHelper = $orderStatusHelper;
        $this->_statusCollection = $statusCollection;
        $this->_orderResourceModel = $orderResourceModel;
        $this->_paymentCommentHelper = $paymentCommentHelper;
        $this->_invoiceSender = $invoiceSender;
        $this->_scopeConfig = $scopeConfig;
        $this->_creditMemoFactory = $creditmemoFactory;
        $this->_creditMemoManagement = $creditmemoManagement;
        $this->_creditMemoSender = $creditmemoSender;
        $this->_config = $config;
        $this->_orderSender = $orderSender;
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
        $transaction['status'] = $this->_responseReader->readTransactionStatus($response);
        $transaction['statusComment'] = $this->_responseReader->readTransactionStatusComment($response);
        $transaction['amountRefunded'] = $this->_responseReader->readAmountRefunded($response);

        $magentoStatus = $this->_orderStatusHelper->transferStatus($transaction);

        if (empty($magentoStatus)) {
            $magentoStatus = 'pending';
        }

        $this->_updateMagentoOrderStatus($handlingSubject, $magentoStatus, $transaction);

        $documentData['status'] = $response['transactions']['transaction_details']['status'];
        $documentData['reason'] = $response['transactions']['transaction_details']['status_reason'];
        $documentData['order'] = $handlingSubject['order'];

        $this->_documentCreation($documentData);
    }

    /**
     * @param array $handlingSubject
     * @param $magentoStatus
     * @param array $transactionData
     */
    protected function _updateMagentoOrderStatus(array $handlingSubject, $magentoStatus, array $transactionData)
    {
        /**
         * @var Order $order
         */
        $order = $handlingSubject['order'];

        $state = $this->_getStatusState($magentoStatus);

        $transactionData['transactionId'] = $handlingSubject['transactionId'];
        $comment = $this->_getComment($transactionData);

        /**
         * MAIL
         */
        $mailSend = false;
        if (
            $transactionData['status'] . '_' . $transactionData['statusComment'] == 'pending_not_credited_yet'
            || $transactionData['status'] . '_' . $transactionData['statusComment'] == 'untraceable_sofort_bank_account_needed'
        ) {
            if ($this->_config->getSendOrderConfirmation()) {
                $this->_orderSender->send($order);
                $mailSend = true;
            }
        }

        $order->setStatus($magentoStatus)
            ->setState($state)
            ->addStatusToHistory($magentoStatus, $comment, $mailSend);
//            ->addStatusHistoryComment($comment, $mailSend);

        $this->_orderResourceModel->save($order);
    }

    /**
     * @param $status
     * @return mixed
     */
    protected function _getStatusState($status)
    {
        return $this->_statusCollection
            ->addAttributeToFilter('state_table.status', $status)
            ->getFirstItem()
            ->getData('state');
    }

    /**
     * @param array $statusData
     * @return \Magento\Framework\Phrase|mixed
     */
    protected function _getComment(array $statusData)
    {
        $comment = 'Status message not defined.';
        $commentKey = $statusData['status'] . '_' . $statusData['statusComment'];

        return $this->_paymentCommentHelper->getFilledComment($commentKey, $statusData);

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

    /**
     * @param array $statusData
     */
    protected function _documentCreation(array $statusData)
    {
        $this->_invoiceCreation($statusData);
        $this->_creditMemoCreation($statusData);
    }

    /**
     * @param array $statusData
     */
    protected function _invoiceCreation(array $statusData)
    {
        if ($statusData['status'] === 'pending'
            && $statusData['reason'] === 'not_credited_yet'
            && $this->_getCreateInvoiceOption() === 'after_order') {
            $this->_generateInvoice($statusData);
        }

        if ($statusData['status'] === 'received'
            && $statusData['reason'] === 'credited'
            && $this->_getCreateInvoiceOption() === 'after_credited') {
            $this->_generateInvoice($statusData);
        }
    }

    /**
     * @param array $statusData
     */
    protected function _creditMemoCreation(array $statusData)
    {
        if ($statusData['status'] === 'refunded'
            && $statusData['reason'] === 'refunded'
            && $this->_getCreateCreditmemoOption()) {
            $this->_generateCreditmemo($statusData);
        }
    }

    /**
     * @param array $statusData
     */
    protected function _generateCreditmemo(array $statusData)
    {
        /**
         * @var Order $order
         */
        $order = $statusData['order'];

        $this->_generateInvoice($statusData);

        if ($order->canCreditmemo()) {

            $creditmemo = $this->_creditMemoFactory
                ->createByOrder($order);

            $this->_creditMemoManagement
                ->refund($creditmemo, true);

            if ($this->_getSendReceiptEmails()) {
                $this->_creditMemoSender
                    ->send($creditmemo);
            }
        }
    }

    /**
     * @param array $statusData
     */
    protected function _generateInvoice(array $statusData)
    {
        /**
         * @var Order $order
         */
        $order = $statusData['order'];
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();

            $invoice->register();

            $invoice->pay()->save();

            $order->save();

            if ($this->_getSendReceiptEmails()) {
                $this->_invoiceSender->send($invoice);
            }
        }
    }

    /**
     * @return bool
     */
    protected function _getSendReceiptEmails()
    {
        return (bool) $this->_scopeConfig
            ->getValue(
                'payment/sofortueberweisung/send_mail',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * @return bool
     */
    protected function _getCreateCreditmemoOption()
    {
        return (boolean) $this->_scopeConfig
            ->getValue(
                'payment/sofortueberweisung/create_creditmemo',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * @return mixed
     */
    protected function _getCreateInvoiceOption()
    {
        return $this->_scopeConfig
            ->getValue(
            'payment/sofortueberweisung/create_invoice',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
