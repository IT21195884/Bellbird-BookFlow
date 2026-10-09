<?php

declare(strict_types=1);

namespace Bellbird;

use PDO;
use PDOException;

final class AuthRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function createAdministrator(
        array $data
    ): int {
        $statement = $this->pdo->prepare(
            'INSERT INTO administrators (
                name,
                email,
                password_hash
            ) VALUES (
                :name,
                :email,
                :password_hash
            )'
        );

        $statement->execute([
            'name' => trim($data['name']),

            'email' => strtolower(
                trim($data['email'])
            ),

            'password_hash' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function emailExists(
        string $email
    ): bool {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM administrators
             WHERE LOWER(email) = LOWER(:email)'
        );

        $statement->execute([
            'email' => trim($email),
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function authenticate(
        string $email,
        string $password
    ): ?array {
        $statement = $this->pdo->prepare(
            "SELECT
                id,
                name,
                email,
                password_hash
             FROM administrators
             WHERE
                LOWER(email) = LOWER(:email)
                AND status = 'Active'"
        );

        $statement->execute([
            'email' => trim($email),
        ]);

        $administrator = $statement->fetch();

        if (
            $administrator === false ||
            !password_verify(
                $password,
                $administrator['password_hash']
            )
        ) {
            return null;
        }

        unset($administrator['password_hash']);

        return $administrator;
    }

    public static function validateRegistration(
        array $data
    ): array {
        $errors = [];

        $name = trim(
            (string) ($data['name'] ?? '')
        );

        $email = trim(
            (string) ($data['email'] ?? '')
        );

        $password =
            (string) ($data['password'] ?? '');

        $passwordConfirmation =
            (string) (
                $data['password_confirmation'] ?? ''
            );

        if ($name === '') {
            $errors[] = 'Administrator name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors[] =
                'Administrator name must not exceed 100 characters.';
        }

        if ($email === '') {
            $errors[] = 'Email address is required.';
        } elseif (
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $errors[] =
                'Enter a valid email address.';
        }

        if (strlen($password) < 8) {
            $errors[] =
                'Password must contain at least 8 characters.';
        }

        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            $errors[] =
                'Password must contain uppercase, lowercase and numeric characters.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] =
                'Password confirmation does not match.';
        }

        return $errors;
    }

    public static function validateLogin(
        array $data
    ): array {
        $errors = [];

        $email = trim(
            (string) ($data['email'] ?? '')
        );

        $password =
            (string) ($data['password'] ?? '');

        if (
            $email === '' ||
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $errors[] =
                'Enter a valid email address.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        return $errors;
    }
}