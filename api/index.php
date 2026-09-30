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

// Hapus file cache config bawaan jika ada agar tidak bentrok
@unlink('/tmp/config.php');

// 2. Kunci environment default
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/framework/views';
$_ENV['SESSION_DRIVER'] = 'file';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['DB_CONNECTION'] = $_ENV['DB_CONNECTION'] ?? 'mysql';

putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/framework/views');
putenv('SESSION_DRIVER=file');
putenv('CACHE_STORE=array');

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage langsung ke /tmp
$app->useStoragePath('/tmp');

// 3. Tangani request sesuai arsitektur Laravel
if (method_exists($app, 'handleRequest')) {
    $app->handleRequest(Illuminate\Http\Request::capture());
} else {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();
    $kernel->terminate($request, $response);
}
