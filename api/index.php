<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat direktori sementara di /tmp
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

// 2. Load autoload & bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Pastikan storage path terarah ke /tmp
$app->useStoragePath('/tmp');

// 3. INJEKSI LANGSUNG SEMUA DRIVER AGAR TIDAK ADA YANG NULL
$cfg = $app['config'];

$cfg->set('session.driver', 'cookie');
$cfg->set('cache.default', 'array');
$cfg->set('logging.default', 'stderr');
$cfg->set('queue.default', 'sync');
$cfg->set('mail.default', 'log');
$cfg->set('broadcasting.default', 'log');
$cfg->set('view.compiled', '/tmp/framework/views');

// 4. Jalankan Request
$app->handleRequest(Request::capture());
