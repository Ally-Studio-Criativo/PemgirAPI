<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Configure PHP upload limits for the entire application
@ini_set('upload_max_filesize', '20M');
@ini_set('post_max_size', '25M');
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '256M');

// Log early request info (before Laravel boots)
$logFile = __DIR__ . '/../storage/logs/early-request.log';
$logEntry = '[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' | ';
$logEntry .= 'Content-Length: ' . ($_SERVER['CONTENT_LENGTH'] ?? '0') . ' | ';
$logEntry .= 'Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'none') . ' | ';
$logEntry .= 'upload_max: ' . ini_get('upload_max_filesize') . ' | ';
$logEntry .= 'post_max: ' . ini_get('post_max_size') . PHP_EOL;
@file_put_contents($logFile, $logEntry, FILE_APPEND);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
