<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Gateway\Validator;


use Magento\Framework\Message\PhraseFactory;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Sofort\Payment\Gateway\Helper\Error;

/**
 * Class BaseValidator
 * @package Sofort\Payment\Gateway\Validator
 */
class BaseValidator extends AbstractValidator
{
    /**
     * @var Error
     */
    protected $_errorHelper;

    /**
     * BaseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param Error $errorHelper
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Error $errorHelper
    ) {
        parent::__construct($resultFactory);
        $this->_errorHelper = $errorHelper;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];

        $error = $this->_errorHelper->validateResponse($response);

        if (empty($error)) {
            return $this->createResult(true);
        }
        return $this->createResult(false, $error);
    }

}
