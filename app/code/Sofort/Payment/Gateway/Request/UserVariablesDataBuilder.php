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

class UserVariablesDataBuilder implements BuilderInterface
{
    const USERVARIABLE_MAIN = 'user_variables';

    const USERVARIABLE_ITEM = 'user_variable';

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * UserVariablesDataBuilder constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    )
    {
        $this->_configHelper = $configHelper;
    }

    /**
     * Generate base data
     *
     * @param array $buildSubject
     */
    public function build(array $buildSubject)
    {
        $return = [];

        return $return;
    }

}
