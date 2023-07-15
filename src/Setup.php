<?php
declare(strict_types=1);

namespace Freya;

require_once __DIR__ . '/../vendor/autoload.php';

class Setup {
    public static function checkRequire() {
        if (version_compare(PHP_VERSION, '7.2.5', '<')) {
            throw new \Exception('Require PHP 7.2.5 or newer');
        }
        if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_sqlite')) {
            throw new \Exception('Require pdo_mysql / pdo_sqlite module');
        }
        if (!extension_loaded('mbstring')) {
            throw new \Exception('Require mbstring module');
        }
    }
}
