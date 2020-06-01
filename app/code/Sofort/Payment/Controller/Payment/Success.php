<?php

namespace Sofort\Payment\Controller\Payment;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\GatewayCommand;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Sofort\Payment\Gateway\Config\Config;
use Sofort\Payment\Gateway\Helper\PaymentComment;
use Sofort\Payment\Gateway\Request\TransactionRequestDataBuilder;

class Success extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var GatewayCommand
     */
    protected $_transactionCommand;

    /**
     * @var \Sofort\Payment\Helper\Config
     */
    protected $_config;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    public function __construct(
        Context $context,
        Order $order,
        Session $checkoutSession,
        \Sofort\Payment\Helper\Config $config,
        Order\Email\Sender\OrderSender $orderSender
    ) {
        parent::__construct($context);
        $this->_order = $order;
        $this->_checkoutSession = $checkoutSession;
        $this->_transactionCommand = $context->getObjectManager()->create('SofortGetTransactionDataCommand');
        $this->_config = $config;
        $this->_orderSender = $orderSender;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('orderId');

        if (!is_null($orderId)) {
            /**
             * @var OrderInterface
             */
            $order = $this->_order->loadByIncrementId($orderId);

            $this->_checkoutSession
                ->setQuoteId($order->getQuoteId());

            $transactionId = $order->getPayment()->getLastTransId();

//            if ($this->_config->getSendOrderConfirmation()) {
//                $test = $order->getStatusHistoryCollection()->getFirstItem();
//                $this->_orderSender->send($order);
//            }

            $buildSubject = [
                TransactionRequestDataBuilder::SOFORT_TRANSACTION_PARAM => $transactionId,
                'order' => $order
            ];

            $this->_transactionCommand->execute($buildSubject);
        }

        $this->_redirect('checkout/onepage/success/');
    }
}
