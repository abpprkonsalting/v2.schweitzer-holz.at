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
class BaseDataBuilder implements BuilderInterface
{
    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Version
     */
    protected $_versionHelper;

    /**
     * BaseDataBuilder constructor.
     * @param Config $configHelper
     * @param Version $versionHelper
     */
    public function __construct(
        Config $configHelper,
        Version $versionHelper
    )
    {
        $this->_versionHelper = $versionHelper;
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

        // load project id
        $return['project_id'] = $this->_configHelper->getProjectKey();
        $return['interface_version'] = $this->_versionHelper->getModuleSignature();

        return $return;
    }

}
