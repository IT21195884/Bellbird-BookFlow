<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Bellbird\Database;

$pdo = Database::connect($config);

Database::initialise(
    $pdo,
    dirname(__DIR__) . '/database/schema.sql'
);

echo "Bellbird BookFlow database created successfully.\n";