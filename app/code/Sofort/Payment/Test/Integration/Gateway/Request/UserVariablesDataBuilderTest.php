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


use Magento\Payment\Gateway\Request\BuilderInterface;

class UserVariablesDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_dataBuilder;

    protected function setUp()
    {
        $this->_dataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Sofort\Payment\Gateway\Request\UserVariablesDataBuilder');
    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_dataBuilder instanceof BuilderInterface,
            get_class($this->_dataBuilder) . ' is present.'
        );
    }

    public function testStructure()
    {
        $this->markTestIncomplete('ReasonDataBuilderTest -> testStructure in development.');
        $resultEmpty = $this->_dataBuilder->build([]);
        $this->assertEmpty($resultEmpty, 'Result is not empty.');
        $resultNotEmpty = $this->_dataBuilder->build(['test']);
        $this->assertNotEmpty($resultNotEmpty, 'Result is empty.');
    }
}
