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
use Magento\Framework\App\Helper\Context;
use Sofort\Payment\Gateway\Exception\ResponseException;
use Sofort\Payment\Gateway\Logger\SofortLoggerInterface;

/**
 * Class Error
 * @package Sofort\Payment\Gateway\Helper
 */
class Error extends AbstractHelper
{
    /**
     * Node identifier
     */
    const SOFORT_BASE_ERROR = 'errors';

    /**
     * Node identifier
     */
    const SOFORT_NODE_ERROR = 'error';

    /**
     * Node identifier
     */
    const SOFORT_NODE_CODE = 'code';

    /**
     * Node identifier
     */
    const SOFORT_NODE_MESSAGE = 'message';

    /**
     * Node identifier
     */
    const SOFORT_NODE_FIELD = 'field';

    /**
     * @var SofortLoggerInterface
     */
    protected $_sofortLogger;

    /**
     * Error constructor.
     * @param Context $context
     * @param SofortLoggerInterface $sofortLogger
     */
    public function __construct(
        Context $context,
        SofortLoggerInterface $sofortLogger
    ) {
        parent::__construct($context);
        $this->_sofortLogger = $sofortLogger;
    }


    /**
     * @param array $response
     * @return array
     */
    public function validateResponse($response = array())
    {
        $return = [];
        $return = array_merge($return, $this->_validateErrorCodes($response));

        if (!empty($return)) {
            foreach ($return as $item) {
                $this->_sofortLogger->error($item);
            }
            $return = [_('Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.')];
        }

        return $return;
    }

    /**
     * @param array $response
     * @throws ResponseException
     */
    protected function _validateNotEmpty($response = array())
    {
        if (empty($response)) {
            return 'No Response present';
        }
    }

    /**
     * @param array $response
     * @return array
     */
    protected function _validateErrorCodes($response = array())
    {
        $errors = [];
        if (isset($response[self::SOFORT_BASE_ERROR][self::SOFORT_NODE_ERROR][self::SOFORT_NODE_CODE])) {
            $errors = $this->_collectErrors($response[self::SOFORT_BASE_ERROR]);
        }

        if (!empty($errors)) {
            return $errors;
        }

        return $errors;
    }

    /**
     * @param array $response
     * @return array
     */
    protected function _collectErrors($response = array())
    {
        $return = array();

        $error = $response;
        foreach ($response as $error) {
            $message = '';
            if (isset($error[self::SOFORT_NODE_CODE])) {
                $message = $error[self::SOFORT_NODE_CODE] . '-';
            }

            if (isset($error[self::SOFORT_NODE_MESSAGE])) {
                $message .= $error[self::SOFORT_NODE_MESSAGE];
            }

            if (!empty($message)) {
                $return[] = $message;
            }
        }

        return $return;
    }
}
