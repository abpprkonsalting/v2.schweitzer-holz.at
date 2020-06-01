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

class BaseDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Gateway\Request\BuilderComposite
     */
    protected $_baseDataBuilder;

    protected function setUp()
    {
        $this->_baseDataBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Sofort\Payment\Gateway\Request\BaseDataBuilder');
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
        $this->assertArrayHasKey('project_id', $result, 'project_id is not present');
        $this->assertArrayHasKey('interface_version', $result, 'interface_version is not present');
        $this->assertEquals('Magento_2.0.13/SOFORT_3.2.6', $result['interface_version'], 'Wrong interface_version present.');

    }
}
