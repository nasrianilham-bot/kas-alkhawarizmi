<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Siapkan semua folder storage di /tmp
$dirs = [
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
    '/tmp/framework/cache/data',
    '/tmp/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Set environment fallback untuk Serverless
$fallbacks = [
    'APP_STORAGE' => '/tmp',
    'VIEW_COMPILED_PATH' => '/tmp/framework/views',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'CACHE_DRIVER' => 'array',
    'LOG_CHANNEL' => 'stderr',
];

foreach ($fallbacks as $k => $v) {
    putenv("{$k}={$v}");
    $_ENV[$k] = $_ENV[$k] ?? $v;
    $_SERVER[$k] = $_SERVER[$k] ?? $v;
}

// 3. Load Autoload & App
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Pastikan storage path terarah ke /tmp
$app->useStoragePath('/tmp');

// 4. Kunci konfigurasi kritis sebelum request di-handle
$app->booting(function () use ($app) {
    $config = $app['config'];
    if (!$config->get('session.driver')) {
        $config->set('session.driver', 'cookie');
    }
    if (!$config->get('cache.default')) {
        $config->set('cache.default', 'array');
    }
    if (!$config->get('logging.default')) {
        $config->set('logging.default', 'stderr');
    }
    $config->set('view.compiled', '/tmp/framework/views');
});

// 5. Jalankan Request
$app->handleRequest(Request::capture());
