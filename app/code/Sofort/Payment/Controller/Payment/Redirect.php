<?php

namespace Sofort\Payment\Controller\Payment;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Sofort\Payment\Gateway\Helper\PaymentComment;

class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var PaymentComment
     */
    protected $_paymentCommentHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        PaymentComment $paymentCommentHelper,
        \Magento\Checkout\Model\Session $checkOutSession
    ) {
        parent::__construct($context);

        $this->_customerSession = $customerSession;
        $this->_paymentCommentHelper = $paymentCommentHelper;
        $this->_checkoutSession = $checkOutSession;
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $data['sofort_transaction_id'] = $order->getPayment()->getAdditionalInformation('sofort_transaction_id');
        $comment = $this->_paymentCommentHelper->getFilledComment('redirect', $data);

        $order->addStatusHistoryComment($comment);

        $order->getPayment()->setLastTransId($order->getPayment()->getAdditionalInformation('sofort_transaction_id'));
//        $payment = $order->getPayment();
//        $payment->setAdditionalInformation($comment);

        $order->save();

        $this->_redirect(
            $this->_customerSession->getPaymentUrl()
        );
    }
}
