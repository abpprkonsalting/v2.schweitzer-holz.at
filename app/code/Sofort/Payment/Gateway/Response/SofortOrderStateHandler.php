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
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Sofort\Payment\Gateway\Exception\ResponseException;
use Sofort\Payment\Gateway\Helper\Error;
use Sofort\Payment\Gateway\Helper\ResponseReader;
use Sofort\Payment\Gateway\Helper\SubjectReader;

/**
 * Class SofortOrderStateHandler
 * @package Sofort\Payment\Gateway\Response
 */
class SofortOrderStateHandler implements HandlerInterface
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
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * SofortOrderStateHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Error $errorHelper
     * @param ResponseReader $responseReader
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SubjectReader $subjectReader,
        Error $errorHelper,
        ResponseReader $responseReader,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->subjectReader = $subjectReader;
        $this->_errorHelper = $errorHelper;
        $this->_responseReader = $responseReader;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
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
        $payment = $this->subjectReader->readPayment($handlingSubject);
        $orderStatus = $payment->getPayment()->getMethodInstance()->getConfigData('order_status');
        $orderState = $this->_getOrderstate($orderStatus);
        $handlingSubject['stateObject']->setData('is_notified', 0);

        $payment->getOrder()->setCustomerNoteNotify(0);


        if (!empty($orderState)) {
            $handlingSubject['stateObject']->setData('state', $orderState);
            $handlingSubject['stateObject']->setData('status', $orderStatus);
        }
    }

    /**
     * @param $orderStatus
     * @return string
     */
    protected function _getOrderstate($orderStatus)
    {
        $orderState = '';
        if ($orderStatus == Order::STATE_PENDING_PAYMENT) {
            $orderState = Order::STATE_PENDING_PAYMENT;
        } elseif ($orderStatus == Order::STATE_PAYMENT_REVIEW) {
            $orderState = Order::STATE_PAYMENT_REVIEW;
        }

        return $orderState;
    }
}
