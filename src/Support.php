<?php

declare(strict_types=1);

namespace Bellbird;

final class Support
{
    public static function escape(mixed $value): string
    {
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
        $submittedToken = $_POST['csrf_token'] ?? '';

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
        $message = $_SESSION['message'] ?? null;

        unset($_SESSION['message']);

        return is_array($message) ? $message : null;
    }

    public static function redirect(string $page): never
    {
        header(
            'Location: index.php?page=' . urlencode($page)
        );

        exit;
    }
}