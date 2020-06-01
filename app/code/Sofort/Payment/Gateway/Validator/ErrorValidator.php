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


use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Sofort\Payment\Gateway\Helper\Error;

/**
 * Class ErrorValidator
 * @package Sofort\Payment\Gateway\Validator
 */
class ErrorValidator extends AbstractValidator
{
    /**
     * @var Error
     */
    protected $_errorHelper;

    /**
     * ErrorValidator constructor.
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
     */
    public function validate(array $validationSubject)
    {
        $valid = true;
        $message = [];
        $result = $this->_errorHelper->validateResponse($validationSubject['response']);

        if (!empty($result)) {
            $valid = false;
            $message = $result;
        }

        $this->createResult($valid, $message);
    }

}
