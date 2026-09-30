<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat folder temporary di /tmp
$dirs = [
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
    '/tmp/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 3. Paksa baca file .env jika ada di root project
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// 4. Kunci nilai default driver kritis langsung ke superglobal
$_ENV['APP_STORAGE'] = '/tmp';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/framework/views';
$_ENV['LOG_CHANNEL'] = $_ENV['LOG_CHANNEL'] ?? 'stderr';
$_ENV['SESSION_DRIVER'] = $_ENV['SESSION_DRIVER'] ?? 'cookie';
$_ENV['CACHE_STORE'] = $_ENV['CACHE_STORE'] ?? 'array';
$_ENV['CACHE_DRIVER'] = $_ENV['CACHE_DRIVER'] ?? 'array';

foreach ($_ENV as $k => $v) {
    if (is_string($v)) {
        putenv("{$k}={$v}");
        $_SERVER[$k] = $v;
    }
}

// 5. Bootstrap Laravel App
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp');

// 6. Jalankan request secara normal
$app->handleRequest(Request::capture());
