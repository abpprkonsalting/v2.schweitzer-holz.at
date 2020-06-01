<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Sofort\Payment\Helper\Config;
use Sofort\Payment\Helper\Version;

/**
 * Class BaseDataBuilder
 * @package Sofort\Payment\Gateway\Request
 */
class TransactionRequestDataBuilder implements BuilderInterface
{
    const SOFORT_TRANSACTION_PARAM = 'transactionId';

    const SOFORT_REQUEST_TRANSACTION = 'transaction_request';

    const SOFORT_REQUEST_TRANSACTION_ATTRIBUTE = 'version';

    const SOFORT_REQUEST_TRANSACTION_ATTRIBUTE_VALUE = '2';

    const SOFORT_TRANSACTION_NODE = 'transaction';

    /**
     * Generate base data
     *
     * $buildSubject => $key = const transactionId
     *                  $vakue = transactionId
     *
     * @param array $buildSubject
     */
    public function build(array $buildSubject)
    {
        $return = [];

        if (
            isset($buildSubject[self::SOFORT_TRANSACTION_PARAM])
            && !empty($buildSubject[self::SOFORT_TRANSACTION_PARAM])
        ) {
            $mainValue['@attributes'] = [
                self::SOFORT_REQUEST_TRANSACTION_ATTRIBUTE => self::SOFORT_REQUEST_TRANSACTION_ATTRIBUTE_VALUE
            ];
            $return[self::SOFORT_TRANSACTION_NODE] = $buildSubject[self::SOFORT_TRANSACTION_PARAM];
        }

        return $return;
    }

}
