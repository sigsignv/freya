<?php
declare(strict_types=1);

namespace Freya;

use Symfony\Component\HttpFoundation\Response;

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

    public static function showError(string $message): Response {
        $title = "エラーが発生しました";
        $css = FREO_CSS_DIR . 'common.css';

        $html = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <link rel="stylesheet" href="{$css}">
</head>
<body>
    <h1>Freya</h1>
    <h2>{$title}</h2>
    <p>{$message}</p>
</body>
</html>
EOD;

        $response = new Response();
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($html);

        return $response;
    }
}
