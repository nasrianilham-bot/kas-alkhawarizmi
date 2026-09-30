<?php

// 1. Buat direktori kerja sementara di /tmp
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

// 2. Set environment langsung ke superglobal dan getenv
$envs = [
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/framework/views',
    'SESSION_DRIVER' => 'file',
    'CACHE_STORE' => 'array',
    'CACHE_DRIVER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($envs as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Arahkan storage path ke /tmp
$app->useStoragePath('/tmp');

// 4. Inisialisasi HTTP Kernel & paksa konfigurasi
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();

// Bootstrap framework lebih awal agar config terisi penuh
$kernel->bootstrap();

// Kunci konfigurasi kritis agar createDriver tidak menerima nilai null
$app['config']->set('session.driver', 'file');
$app['config']->set('session.files', '/tmp/framework/sessions');
$app['config']->set('cache.default', 'array');
$app['config']->set('logging.default', 'stderr');
$app['config']->set('view.compiled', '/tmp/framework/views');

// 5. Jalankan Request
$response = $kernel->handle($request)->send();

$kernel->terminate($request, $response);
