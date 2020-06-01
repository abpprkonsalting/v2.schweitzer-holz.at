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


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Model\Order;

/**
 * Class OrderStatus
 * @package Sofort\Payment\Gateway\Helper
 */
class OrderStatus extends AbstractHelper
{
    const SOFORT_STATUS_CONFIG_BASE = 'payment/sofortueberweisung/status_';

    protected $_statusMessages = [
        'loss' => 'not_credited',
        'pending' => 'not_credited_yet',
        'received' => 'credited',
        'refunded' => ['refunded','compensation'],
        'untraceable' => 'sofort_bank_account_needed'
    ];

    /**
     * Transfer Sofort status to Magento status
     *
     * @param array $transactionData
     * @return string
     */
    public function transferStatus(array $transactionData)
    {
        $magentoStatus = 'unknown';

        $status = $transactionData['status'];
        $statusComment = $transactionData['statusComment'];

        if (
            $status == 'refunded'
            && in_array($statusComment, $this->_statusMessages[$status])
        ) {
            $path = self::SOFORT_STATUS_CONFIG_BASE . $status . '_' . $statusComment;

            $magentoStatus = $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        } else if (
            array_key_exists($status, $this->_statusMessages)
            && $this->_statusMessages[$status] == $statusComment
        ) {
            if ($status === 'untraceable' && $statusComment === 'sofort_bank_account_needed') {
                $status = 'pending';
                $statusComment = 'not_credited_yet';
            }

            $path = self::SOFORT_STATUS_CONFIG_BASE . $status . '_' . $statusComment;

            $magentoStatus = $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        }

        return $magentoStatus;
    }
}
