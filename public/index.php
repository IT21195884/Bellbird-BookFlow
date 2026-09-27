<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Bellbird\Database;
use Bellbird\Support;

$pdo = Database::connect($config);

Database::initialise(
    $pdo,
    dirname(__DIR__) . '/database/schema.sql'
);

$page = $_GET['page'] ?? 'dashboard';

$allowedPages = [
    'dashboard',
    'stock',
    'customers',
    'orders',
];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);

    $page = '404';
}

$message = Support::getMessage();

require dirname(__DIR__) . '/views/header.php';

require dirname(__DIR__)
    . '/views/'
    . $page
    . '.php';

require dirname(__DIR__) . '/views/footer.php';