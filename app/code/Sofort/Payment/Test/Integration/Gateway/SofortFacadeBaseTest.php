<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\payment\Test\Integration\Gateway;


use Magento\Payment\Model\InfoInterface;

/**
 * Class SofortFacadeBaseTest
 * @package Sofort\payment\Test\Integration\Gateway
 */
class SofortFacadeBaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Method\Adapter
     */
    protected $_facade;

    /**
     *
     */
    protected function setUp()
    {
        $this->_facade = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('SofortFacade');
    }

    /**
     * Test Facade
     */
    public function testFacadeInstance()
    {
        $this->assertTrue(
            $this->_facade instanceof \Magento\Payment\Model\Method\Adapter,
            get_class($this->_facade) . ' is present.');
        $this->assertEquals($this->_facade->getCode(),
            \Sofort\Payment\Model\Ui\ConfigProvider::CODE,
            $this->_facade->getCode() . ' is set.'
        );
        $this->assertEquals(1, $this->_facade->isActive(1), 'Sofort payment is not active.');
        $this->assertEquals('Sofort.', $this->_facade->getTitle()->getText(), 'The title is wrong.');
    }
}
