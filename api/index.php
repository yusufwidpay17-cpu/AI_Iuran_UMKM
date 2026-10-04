<?php

// Forward Vercel serverless requests to Laravel
define('LARAVEL_START', microtime(true));

// Create temporary directory structure for storage on Vercel (/tmp is writeable)
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/logs',
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Check SQLite DB in /tmp if sqlite driver is used
$sqliteFile = '/tmp/database.sqlite';
if (!file_exists($sqliteFile)) {
    @touch($sqliteFile);
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
