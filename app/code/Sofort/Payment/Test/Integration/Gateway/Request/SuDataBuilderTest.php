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
use Sofort\Payment\Gateway\Request\SuDataBuilder;

class SuDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_baseDataBuilder;

    protected function setUp()
    {
        $this->_baseDataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(SuDataBuilder::class);
    }

    public function testBuilderInstance()
    {
        $this->assertTrue(
            $this->_baseDataBuilder instanceof BuilderInterface,
            get_class($this->_baseDataBuilder) . ' is present.'
        );
    }

    public function testStructure()
    {
        $result = $this->_baseDataBuilder->build([]);
        $this->assertTrue(is_array($result), 'Result is no array.');
        $this->assertArrayHasKey('su', $result, 'su is not present');

    }
}
