<?php

use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals true
 */
final class VersionTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../libs/freo/version.php';
    }

    public function testVersion(): void 
    {
        $this->assertTrue(defined('FREO_VERSION'));
    }
}
