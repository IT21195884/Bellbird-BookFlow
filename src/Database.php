<?php

declare(strict_types=1);

namespace Bellbird;

use PDO;

final class Database
{
    public static function connect(array $config): PDO
    {
        $directory = dirname($config['db_path']);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $pdo = new PDO(
            'sqlite:' . $config['db_path']
        );

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        $pdo->exec('PRAGMA foreign_keys = ON');

        return $pdo;
    }

    public static function initialise(
        PDO $pdo,
        string $schemaPath
    ): void {
        $schema = file_get_contents($schemaPath);

        if ($schema === false) {
            throw new \RuntimeException(
                'Unable to read the database schema.'
            );
        }

        $pdo->exec($schema);
    }
}