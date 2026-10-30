<?php

// Forward Vercel serverless requests to Laravel
define('LARAVEL_START', microtime(true));

// Create temporary directory structure for storage on Vercel (/tmp is writeable)
$tmpStorage = '/tmp/storage';
if (!is_dir($tmpStorage . '/framework/views')) {
    @mkdir($tmpStorage . '/framework/views', 0755, true);
    @mkdir($tmpStorage . '/framework/cache', 0755, true);
    @mkdir($tmpStorage . '/framework/sessions', 0755, true);
    @mkdir($tmpStorage . '/framework/testing', 0755, true);
    @mkdir($tmpStorage . '/logs', 0755, true);
}

// Check SQLite DB in /tmp only if sqlite driver is used
if (getenv('DB_CONNECTION') === 'sqlite') {
    $sqliteFile = '/tmp/database.sqlite';
    if (!file_exists($sqliteFile)) {
        @touch($sqliteFile);
    }
}

// Load Composer Autoload
require __DIR__ . '/../vendor/autoload.php';

// Load Laravel Bootstrap
$app = require __DIR__ . '/../bootstrap/app.php';

// Override storage path to /tmp/storage
$app->useStoragePath($tmpStorage);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
