<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\payment\Test\Integration\Gateway\Request;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Sofort\Payment\Gateway\Data\Rewrite\Order\OrderAdapter;
use Sofort\Payment\Gateway\Helper\Url;
use Sofort\Payment\Helper\Array2Xml;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Url
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Sofort\Payment\Gateway\Helper\Url::class);
    }

    public function testGetNotificationUrl()
    {
        $result = $this->_helper->getNotificationUrl(100);
    }
}
