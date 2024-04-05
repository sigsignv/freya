<?php

use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals enabled
 */
final class MimeTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../libs/freo/common.php';
    }

    public function testMime(): void
    {
        $this->assertEquals(freo_mime('/path/to/example.atom'), 'application/atom+xml');
    }
}
