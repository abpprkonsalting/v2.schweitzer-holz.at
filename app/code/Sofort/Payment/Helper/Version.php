<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Version
 * @package Sofort\Payment\Helper
 */
class Version extends AbstractHelper
{
    const MODULE_NAME = 'Sofort_Payment';

    const SYSTEM_SHORT_TAG = 'MAGENTO2';

    const PROJECT_KEY = 'SOFORT';

    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetaData;

    /**
     * Version constructor.
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->_moduleList = $moduleList;
        $this->_productMetaData = $productMetadata;
    }

    /**
     * Return Version of Module
     *
     * @return array|null
     */
    public function getModulVersion()
    {
        return $this->_moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * @return string
     */
    public function getModuleSignature()
    {
        $magentoVersionString = $this->_productMetaData->getName() . '_' . $this->_productMetaData->getVersion();

        $sofortVersionString = self::PROJECT_KEY . '_' . $this->getModulVersion();

        return $magentoVersionString . '/' . $sofortVersionString;

        return self::SYSTEM_SHORT_TAG . '_' . $this->getModulVersion();
    }
}
