<?php

namespace Sofort\Payment\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\GatewayCommand;
use Magento\Sales\Model\Order;
use Sofort\Payment\Gateway\Logger\SofortLoggerInterface;
use Sofort\Payment\Gateway\Request\TransactionRequestDataBuilder;

class Notification extends \Magento\Framework\App\Action\Action
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
     * @var SofortLoggerInterface
     */
    protected $_sofortLogger;

    /**
     * Notification constructor.
     * @param Context $context
     * @param Order $order
     * @param SofortGetTransactionDataCommand $transactionCommand
     */
    public function __construct(
        Context $context,
        Order $order,
        SofortLoggerInterface $sofortLogger
    ) {
        parent::__construct($context);
        $this->_transactionCommand = $context->getObjectManager()->create('SofortGetTransactionDataCommand');
        $this->_order = $order;
        $this->_sofortLogger = $sofortLogger;
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
            $order = $this->_order->loadByIncrementId($orderId);
            if ($order->getId()) {
                $transactionId = $order->getPayment()->getLastTransId();
            }

            if (empty($transactionId)) {
                $this->_sofortLogger->alert('Order without Sofort Payment is requested. Order ID: ' . $orderId);
                return;
            }

            $buildSubject = [
                TransactionRequestDataBuilder::SOFORT_TRANSACTION_PARAM => $transactionId,
                'order' => $order
            ];

            $this->_transactionCommand->execute($buildSubject);
        }
    }
}
