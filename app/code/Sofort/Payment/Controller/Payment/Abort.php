<?php

namespace Sofort\Payment\Controller\Payment;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\GatewayCommand;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Sofort\Payment\Gateway\Helper\PaymentComment;
use Sofort\Payment\Gateway\Request\TransactionRequestDataBuilder;

class Abort extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var GatewayCommand
     */
    protected $_transactionCommand;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $_orderResource;

    /**
     * @var PaymentComment
     */
    protected $_paymentCommentHelper;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * Notification constructor.
     * @param Context $context
     * @param Order $order
     * @param SofortGetTransactionDataCommand $transactionCommand
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        Order $order,
        PaymentComment $paymentCommentHelper,
        Cart $cart
    ) {
        parent::__construct($context);
        $this->_transactionCommand = $context->getObjectManager()->create('SofortGetTransactionDataCommand');
        $this->_order = $order;
        $this->_orderResource = $orderResource;
        $this->_paymentCommentHelper = $paymentCommentHelper;
        $this->_cart = $cart;
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
            /*
             * load order and get transaction id
             * generate request and trigger api for transaction data
             */
            $this->_orderResource->load($this->_order, $orderId, 'increment_id');

            $this->_order->setState('pending');
            $this->_order->cancel();
            $this->_order->addStatusHistoryComment(
                sprintf(
                    __($this->_paymentCommentHelper->getComment('abort')),
                    date('d.m.Y H:i:s')
                )
            );

            $this->_orderResource->save($this->_order);

            foreach ($this->_order->getItemsCollection() as $item) {
                try {
                    $this->_cart->addOrderItem($item);
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Framework\Logger\Monolog')->critical($e);
                }
            }

            $this->_cart->save();

            $this->messageManager->addNotice(sprintf(__('Payment aborted. Time: %s'), date('d.m.Y H:i:s')));

            $this->_redirect('checkout/cart/');

        }
    }
}
