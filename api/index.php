<?php

// 1. Buat folder temporary yang dibutuhkan
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

// 2. Set environment fallback
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/framework/views');
putenv('SESSION_DRIVER=file');
putenv('CACHE_STORE=array');
putenv('CACHE_DRIVER=array');

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Arahkan storage ke /tmp
$app->useStoragePath('/tmp');

// 4. Paksa konfigurasi langsung ke container Laravel
$app->booted(function () use ($app) {
    $config = $app['config'];
    
    // Kunci session driver ke 'file' dan arahkan path ke /tmp
    $config->set('session.driver', 'file');
    $config->set('session.files', '/tmp/framework/sessions');
    
    // Kunci cache ke array (memory)
    $config->set('cache.default', 'array');
    
    // Kunci logging agar tidak menulis file
    $config->set('logging.default', 'stderr');
    
    // Kunci blade view path ke /tmp
    $config->set('view.compiled', '/tmp/framework/views');
});

// 5. Eksekusi request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
