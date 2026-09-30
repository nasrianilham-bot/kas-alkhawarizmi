<?php

// Siapkan folder temporary
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

// Inisialisasi env default jika kosong
$_ENV['APP_STORAGE'] = '/tmp';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/framework/views';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['LOG_CHANNEL'] = 'stderr';

// Jalankan index utama Laravel
require __DIR__ . '/../public/index.php';
