<?php

declare(strict_types=1);

namespace Bellbird;

final class Support
{
    public static function escape(
        mixed $value
    ): string {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): void
    {
        $submittedToken =
            $_POST['csrf_token'] ?? '';

        if (
            !is_string($submittedToken) ||
            !hash_equals(
                self::csrfToken(),
                $submittedToken
            )
        ) {
            http_response_code(419);

            exit(
                'The form session expired. '
                . 'Please return and try again.'
            );
        }
    }

    public static function setMessage(
        string $type,
        string $message
    ): void {
        $_SESSION['message'] = [
            'type' => $type,
            'text' => $message,
        ];
    }

    public static function getMessage(): ?array
    {
        $message =
            $_SESSION['message'] ?? null;

        unset($_SESSION['message']);

        return is_array($message)
            ? $message
            : null;
    }

    public static function redirect(
        string $page
    ): never {
        header(
            'Location: index.php?page='
            . urlencode($page)
        );

        exit;
    }

    public static function login(
        array $administrator
    ): void {
        session_regenerate_id(true);

        $_SESSION['administrator'] = [
            'id' => (int) $administrator['id'],
            'name' => (string) $administrator['name'],
            'email' => (string) $administrator['email'],
        ];
    }

    public static function logout(): void
    {
        unset(
            $_SESSION['administrator'],
            $_SESSION['csrf_token']
        );

        session_regenerate_id(true);
    }

    public static function isAuthenticated(): bool
    {
        return isset(
            $_SESSION['administrator']['id']
        );
    }

    public static function administrator(): ?array
    {
        $administrator =
            $_SESSION['administrator'] ?? null;

        return is_array($administrator)
            ? $administrator
            : null;
    }

    public static function requireAuthentication(): void
    {
        if (self::isAuthenticated()) {
            return;
        }

        self::setMessage(
            'error',
            'Please log in to access that page.'
        );

        self::redirect('login');
    }
}