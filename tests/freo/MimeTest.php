<?php

use PHPUnit\Framework\TestCase;

final class MimeTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../libs/freo/common.php';
    }

    public function testMime(): void
    {
        $this->assertEquals(freo_mime('/path/to/example.html'), 'text/html');
    }

    public function testMimeDefault(): void
    {
        $this->assertEquals(freo_mime('/path/to/example_without_ext'), 'application/octet-stream');
    }
}
