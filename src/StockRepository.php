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

    /*
     * ========================================================
     * NEW-BOOK FUNCTIONS
     * ========================================================
     */

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
     * Find one new-book record by its ID.
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

            'selling_price' =>
                (float) $data['selling_price'],

            'quantity' => (int) $data['quantity'],

            'section' => trim($data['section']),

            'shelf_location' => trim(
                $data['shelf_location']
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Increase or decrease a new-book quantity.
     *
     * The quantity cannot become lower than zero.
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

        return $statement->execute([
            'title' => trim($data['title']),
            'author' => trim($data['author']),

            'isbn' => self::nullableText(
                $data['isbn'] ?? null
            ),

            'cost_price' => self::nullableNumber(
                $data['cost_price'] ?? null
            ),

            'selling_price' =>
                (float) $data['selling_price'],

            'quantity' => (int) $data['quantity'],

            'section' => trim($data['section']),

            'shelf_location' => trim(
                $data['shelf_location']
            ),

            'id' => $id,
        ]);
    }

    /**
     * Validate new-book form information.
     */
    public static function validateNewBook(
        array $data
    ): array {
        $errors = [];

        if (
            trim((string) ($data['title'] ?? '')) === ''
        ) {
            $errors[] = 'Title is required.';
        }

        if (
            trim((string) ($data['author'] ?? '')) === ''
        ) {
            $errors[] = 'Author is required.';
        }

        if (
            trim((string) ($data['section'] ?? '')) === ''
        ) {
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
            !is_numeric(
                $data['selling_price'] ?? null
            ) ||
            (float) $data['selling_price'] < 0
        ) {
            $errors[] =
                'Selling price must be zero or greater.';
        }

        if (
            ($data['cost_price'] ?? '') !== '' &&
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

    /*
     * ========================================================
     * SECOND-HAND COPY FUNCTIONS
     * ========================================================
     */

    /**
     * Return every available second-hand copy.
     */
    public function getAvailableSecondHandCopies(): array
    {
        $statement = $this->pdo->query(
            "SELECT *
             FROM second_hand_copies
             WHERE status = 'Available'
             ORDER BY title, author, id"
        );

        return $statement->fetchAll();
    }

    /**
     * Find one individual second-hand copy by its ID.
     */
    public function findSecondHandCopy(
        int $id
    ): ?array {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM second_hand_copies
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $copy = $statement->fetch();

        if ($copy === false) {
            return null;
        }

        return $copy;
    }

    /**
     * Create one individual second-hand copy.
     */
    public function createSecondHandCopy(
        array $data
    ): int {
        $statement = $this->pdo->prepare(
            'INSERT INTO second_hand_copies (
                title,
                author,
                condition_grade,
                purchase_price,
                selling_price,
                section,
                shelf_location,
                intake_reference,
                intake_date,
                acquisition_source,
                notes
            ) VALUES (
                :title,
                :author,
                :condition_grade,
                :purchase_price,
                :selling_price,
                :section,
                :shelf_location,
                :intake_reference,
                :intake_date,
                :acquisition_source,
                :notes
            )'
        );

        $statement->execute([
            'title' => trim($data['title']),

            'author' => trim($data['author']),

            'condition_grade' =>
                $data['condition_grade'],

            'purchase_price' => self::nullableNumber(
                $data['purchase_price'] ?? null
            ),

            'selling_price' =>
                (float) $data['selling_price'],

            'section' => trim($data['section']),

            'shelf_location' => trim(
                $data['shelf_location']
            ),

            'intake_reference' => self::nullableText(
                $data['intake_reference'] ?? null
            ),

            'intake_date' => self::nullableText(
                $data['intake_date'] ?? null
            ),

            'acquisition_source' => self::nullableText(
                $data['acquisition_source'] ?? null
            ),

            'notes' => self::nullableText(
                $data['notes'] ?? null
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update one individual second-hand copy.
     */
    public function updateSecondHandCopy(
        int $id,
        array $data
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE second_hand_copies
             SET
                title = :title,
                author = :author,
                condition_grade = :condition_grade,
                purchase_price = :purchase_price,
                selling_price = :selling_price,
                section = :section,
                shelf_location = :shelf_location,
                intake_reference = :intake_reference,
                intake_date = :intake_date,
                acquisition_source = :acquisition_source,
                notes = :notes,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        return $statement->execute([
            'title' => trim($data['title']),

            'author' => trim($data['author']),

            'condition_grade' =>
                $data['condition_grade'],

            'purchase_price' => self::nullableNumber(
                $data['purchase_price'] ?? null
            ),

            'selling_price' =>
                (float) $data['selling_price'],

            'section' => trim($data['section']),

            'shelf_location' => trim(
                $data['shelf_location']
            ),

            'intake_reference' => self::nullableText(
                $data['intake_reference'] ?? null
            ),

            'intake_date' => self::nullableText(
                $data['intake_date'] ?? null
            ),

            'acquisition_source' => self::nullableText(
                $data['acquisition_source'] ?? null
            ),

            'notes' => self::nullableText(
                $data['notes'] ?? null
            ),

            'id' => $id,
        ]);
    }

    /**
     * Mark an individual second-hand copy as sold
     * or removed from available stock.
     */
    public function markSecondHandUnavailable(
        int $id,
        string $status
    ): bool {
        $allowedStatuses = [
            'Sold',
            'Removed',
        ];

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            return false;
        }

        $statement = $this->pdo->prepare(
            "UPDATE second_hand_copies
             SET
                status = :status,
                updated_at = CURRENT_TIMESTAMP
             WHERE
                id = :id
                AND status = 'Available'"
        );

        $statement->execute([
            'status' => $status,
            'id' => $id,
        ]);

        return $statement->rowCount() === 1;
    }

    /**
     * Validate second-hand copy information.
     */
    public static function validateSecondHandCopy(
        array $data
    ): array {
        $errors = [];

        if (
            trim((string) ($data['title'] ?? '')) === ''
        ) {
            $errors[] = 'Title is required.';
        }

        if (
            trim((string) ($data['author'] ?? '')) === ''
        ) {
            $errors[] = 'Author is required.';
        }

        $allowedConditions = [
            'As New',
            'Very Good',
            'Good',
            'Fair',
            'Reading Copy',
        ];

        if (
            !in_array(
                $data['condition_grade'] ?? '',
                $allowedConditions,
                true
            )
        ) {
            $errors[] = 'Select a valid condition.';
        }

        if (
            trim((string) ($data['section'] ?? '')) === ''
        ) {
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
            !is_numeric(
                $data['selling_price'] ?? null
            ) ||
            (float) $data['selling_price'] < 0
        ) {
            $errors[] =
                'Selling price must be zero or greater.';
        }

        if (
            ($data['purchase_price'] ?? '') !== '' &&
            (
                !is_numeric($data['purchase_price']) ||
                (float) $data['purchase_price'] < 0
            )
        ) {
            $errors[] =
                'Purchase price must be zero or greater.';
        }

        return $errors;
    }

    /*
     * ========================================================
     * SHARED HELPER FUNCTIONS
     * ========================================================
     */

    /**
     * Convert an empty text field to null.
     */
    private static function nullableText(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    /**
     * Convert an empty number field to null.
     */
    private static function nullableNumber(
        mixed $value
    ): ?float {
        if (
            $value === null ||
            $value === ''
        ) {
            return null;
        }

        return (float) $value;
    }
}