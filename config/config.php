<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$envFile = $projectRoot . '/.env';

if (is_file($envFile)) {
    $lines = file(
        $envFile,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines ?: [] as $line) {
        $line = trim($line);

        if (
            $line === '' ||
            str_starts_with($line, '#') ||
            !str_contains($line, '=')
        ) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim(trim($value), "\"'");

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

$dbPath = getenv('DB_PATH') ?: 'database/bellbird.sqlite';

if (!str_starts_with($dbPath, '/')) {
    $dbPath = $projectRoot . '/' . $dbPath;
}

return [
    'app_env' => getenv('APP_ENV') ?: 'production',

    'app_debug' => filter_var(
        getenv('APP_DEBUG') ?: 'false',
        FILTER_VALIDATE_BOOL
    ),

    'app_url' => getenv('APP_URL') ?: 'http://localhost:8000',

    'app_secret' => getenv('APP_SECRET')
        ?: 'development-only-secret',

    'db_path' => $dbPath,
];

//Managing applications settings separately from the source code