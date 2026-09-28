<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config/config.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'Bellbird\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $className = substr($class, strlen($prefix));

    $file = __DIR__
        . '/src/'
        . str_replace('\\', '/', $className)
        . '.php';

    if (is_file($file)) {
        require $file;
    }
});

if ($config['app_debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}