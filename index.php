<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/digitalskp-main/public';

if ($uri !== '/' && file_exists($publicPath . $uri)) {
    $ext = pathinfo($uri, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($publicPath . $uri);
        exit;
    }
}

$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
require $publicPath . '/index.php';
