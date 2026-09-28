<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$directories = [
    $projectRoot . '/config',
    $projectRoot . '/public',
    $projectRoot . '/scripts',
    $projectRoot . '/src',
    $projectRoot . '/views',
];

$excludedFiles = [
    realpath(__FILE__),
];

$filesChecked = 0;
$failures = 0;

echo "Bellbird BookFlow quality check\n";
echo "================================\n\n";

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        echo "Skipped missing directory: {$directory}\n";
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $directory,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        if (strtolower($file->getExtension()) !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $realPath = realpath($path);

        if (in_array($realPath, $excludedFiles, true)) {
            continue;
        }

        $filesChecked++;

        $command = sprintf(
            'php -l %s 2>&1',
            escapeshellarg($path)
        );

        exec($command, $output, $exitCode);

        if ($exitCode === 0) {
            echo "[PASS] {$path}\n";
        } else {
            $failures++;
            echo "[FAIL] {$path}\n";
            echo implode(PHP_EOL, $output) . PHP_EOL;
        }

        $output = [];
    }
}

echo "\nFiles checked: {$filesChecked}\n";
echo "Failures: {$failures}\n";

if ($filesChecked === 0) {
    fwrite(
        STDERR,
        "No PHP files were found.\n"
    );

    exit(1);
}

if ($failures > 0) {
    fwrite(
        STDERR,
        "Quality check failed.\n"
    );

    exit(1);
}

echo "Quality check passed.\n";
exit(0);