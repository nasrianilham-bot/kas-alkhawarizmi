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

// 2. Kunci APP_KEY dan driver kritis langsung ke Environment sebelum Laravel boot
$envVars = [
    'APP_KEY'            => 'base64:c2FtcGxlLWtleS1mb3ItdmVyY2VsLWRlcGxveS0xMjM0NTY=',
    'APP_STORAGE'        => '/tmp',
    'VIEW_COMPILED_PATH' => '/tmp/framework/views',
    'SESSION_DRIVER'     => 'cookie',
    'CACHE_STORE'        => 'array',
    'LOG_CHANNEL'        => 'stderr',
];

foreach ($envVars as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

// 3. Load Autoload & App
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp');

// 4. Eksekusi Request
$app->handleRequest(Request::capture());
