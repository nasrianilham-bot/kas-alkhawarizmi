<?php

// 1. Siapkan semua struktur folder di /tmp
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

// 2. Kunci variabel environment penting untuk serverless
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/framework/views');
putenv('SESSION_DRIVER=file');
putenv('CACHE_STORE=array');
putenv('CACHE_DRIVER=array');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

// 3. Muat vendor & inisialisasi Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage utama ke /tmp
$app->useStoragePath('/tmp');

// 4. Jalankan request (kompatibel Laravel modern)
if (interface_exists(Illuminate\Contracts\Http\Kernel::class) && $app->bound(Illuminate\Contracts\Http\Kernel::class)) {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();
    $kernel->terminate($request, $response);
} else {
    $app->handleRequest(Illuminate\Http\Request::capture());
}
