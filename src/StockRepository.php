<?php

declare(strict_types=1);

namespace Bellbird;

use PDO;

final class StockRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * Return all new-book records.
     */
    public function getAllNewBooks(): array
    {
        $statement = $this->pdo->query(
            'SELECT *
             FROM new_books
             ORDER BY title, author'
        );

        return $statement->fetchAll();
    }

    /**
     * Find one new-book record using its ID.
     */
    public function findNewBook(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM new_books
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $book = $statement->fetch();

        if ($book === false) {
            return null;
        }

        return $book;
    }

    /**
     * Create a new-book record.
     */
    public function createNewBook(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO new_books (
                title,
                author,
                isbn,
                cost_price,
                selling_price,
                quantity,
                section,
                shelf_location
            ) VALUES (
                :title,
                :author,
                :isbn,
                :cost_price,
                :selling_price,
                :quantity,
                :section,
                :shelf_location
            )'
        );

        $statement->execute([
            'title' => trim($data['title']),
            'author' => trim($data['author']),
            'isbn' => self::nullableText(
                $data['isbn'] ?? null
            ),
            'cost_price' => self::nullableNumber(
                $data['cost_price'] ?? null
            ),
            'selling_price' => (float) $data['selling_price'],
            'quantity' => (int) $data['quantity'],
            'section' => trim($data['section']),
            'shelf_location' => trim(
                $data['shelf_location']
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Increase or decrease the quantity.
     *
     * The database update will only occur when the resulting
     * quantity is zero or greater.
     */
    public function adjustNewBookQuantity(
        int $id,
        int $change
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE new_books
             SET
                quantity = quantity + :change,
                updated_at = CURRENT_TIMESTAMP
             WHERE
                id = :id
                AND quantity + :change >= 0'
        );

        $statement->execute([
            'change' => $change,
            'id' => $id,
        ]);

        return $statement->rowCount() === 1;
    }

    /**
     * Update an existing new-book record.
     */
    public function updateNewBook(
        int $id,
        array $data
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE new_books
             SET
                title = :title,
                author = :author,
                isbn = :isbn,
                cost_price = :cost_price,
                selling_price = :selling_price,
                quantity = :quantity,
                section = :section,
                shelf_location = :shelf_location,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $statement->execute([
            'title' => trim($data['title']),
            'author' => trim($data['author']),
            'isbn' => self::nullableText(
                $data['isbn'] ?? null
            ),
            'cost_price' => self::nullableNumber(
                $data['cost_price'] ?? null
            ),
            'selling_price' => (float) $data['selling_price'],
            'quantity' => (int) $data['quantity'],
            'section' => trim($data['section']),
            'shelf_location' => trim(
                $data['shelf_location']
            ),
            'id' => $id,
        ]);

        return $statement->rowCount() === 1;
    }

    /**
     * Validate new-book form information.
     */
    public static function validateNewBook(
        array $data
    ): array {
        $errors = [];

        if (trim((string) ($data['title'] ?? '')) === '') {
            $errors[] = 'Title is required.';
        }

        if (trim((string) ($data['author'] ?? '')) === '') {
            $errors[] = 'Author is required.';
        }

        if (trim((string) ($data['section'] ?? '')) === '') {
            $errors[] = 'Section is required.';
        }

        if (
            trim(
                (string) ($data['shelf_location'] ?? '')
            ) === ''
        ) {
            $errors[] = 'Shelf location is required.';
        }

        if (
            !is_numeric($data['selling_price'] ?? null) ||
            (float) $data['selling_price'] < 0
        ) {
            $errors[] =
                'Selling price must be zero or greater.';
        }

        if (
            isset($data['cost_price']) &&
            $data['cost_price'] !== '' &&
            (
                !is_numeric($data['cost_price']) ||
                (float) $data['cost_price'] < 0
            )
        ) {
            $errors[] =
                'Cost price must be zero or greater.';
        }

        $quantity = filter_var(
            $data['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $quantity === false ||
            $quantity < 0
        ) {
            $errors[] =
                'Quantity must be a whole number of zero or greater.';
        }

        return $errors;
    }

    private static function nullableText(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    private static function nullableNumber(
        mixed $value
    ): ?float {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}