<?php
// Debug endpoint - shows PHP and Laravel info
header('Content-Type: application/json');

$info = [
    'php_version' => PHP_VERSION,
    'server_time' => date('Y-m-d H:i:s'),
    'timezone' => date_default_timezone_get(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
];

// Check if Laravel is available
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $info['composer_autoload'] = 'OK';

    try {
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $info['laravel_bootstrap'] = 'OK';
        $info['app_name'] = config('app.name', 'N/A');
        $info['app_env'] = config('app.env', 'N/A');
        $info['app_debug'] = config('app.debug', 'N/A');
    } catch (\Throwable $e) {
        $info['laravel_bootstrap'] = 'FAILED';
        $info['bootstrap_error'] = $e->getMessage();
        $info['bootstrap_file'] = $e->getFile();
        $info['bootstrap_line'] = $e->getLine();
    }
} catch (\Throwable $e) {
    $info['composer_autoload'] = 'FAILED';
    $info['autoload_error'] = $e->getMessage();
}

// Check environment variables
$info['env_vars'] = [
    'APP_KEY' => isset($_ENV['APP_KEY']) ? (empty($_ENV['APP_KEY']) ? 'EMPTY' : 'SET') : 'NOT SET',
    'APP_ENV' => $_ENV['APP_ENV'] ?? 'NOT SET',
    'APP_DEBUG' => $_ENV['APP_DEBUG'] ?? 'NOT SET',
    'DATABASE_URL' => isset($_ENV['DATABASE_URL']) ? 'SET' : 'NOT SET',
    'DB_HOST' => isset($_ENV['DB_HOST']) ? 'SET' : 'NOT SET',
    'PORT' => $_ENV['PORT'] ?? 'NOT SET',
];

echo json_encode($info, JSON_PRETTY_PRINT);
