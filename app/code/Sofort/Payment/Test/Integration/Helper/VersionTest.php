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

use Sofort\Payment\Helper\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Version
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Sofort\Payment\Helper\Version');
    }

    /**
     * Test version reading of Module
     */
    public function testGetModulVersion()
    {
        $this->assertTrue($this->_helper instanceof Version, get_class($this->_helper) . ' is present.');
        $this->assertNotNull($this->_helper->getModulVersion(), 'No version returned.');
        $this->assertEquals(
            '3.2.6',
            $this->_helper->getModulVersion(),
            $this->_helper->getModulVersion() . ' Wrong version returned.'
        );
    }

    /**
     * Test module signature for API value interface_version
     */
    public function testGetModuleSignature()
    {
        $subPartStart = ['Magento', 'SOFORT'];

        $moduleSignature = $this->_helper->getModuleSignature();
        $this->assertStringStartsWith('Magento', $moduleSignature, 'Module signature doe not start with Magento');
        $mainParts = explode('/', $moduleSignature);
        $this->assertCount(2, $mainParts, 'There are not 2 Main parts present');
        foreach ($mainParts as $mainPart) {
            $subParts = explode('_', $mainPart);
            $this->assertCount(2, $subParts, 'There are not 2 sub parts present.');
            $this->assertTrue(in_array($subParts[0], $subPartStart), $subParts[0] . ' is not a legal Part.');
        }

        $this->assertEquals("Magento_2.0.13/SOFORT_3.2.6", $this->_helper->getModuleSignature(), $this->_helper->getModuleSignature() . ' Wrong signature returned.');
    }
}
