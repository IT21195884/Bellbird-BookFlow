<?php

declare(strict_types=1);

namespace Bellbird;

use PDO;

final class GalleryRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * Search all available new and second-hand books.
     */
    public function searchBooks(
        string $search = '',
        string $section = '',
        string $stockType = '',
        ?float $maximumPrice = null
    ): array {
        $sql = '
            SELECT *
            FROM (
                SELECT
                    id,
                    "New" AS stock_type,
                    title,
                    author,
                    isbn,
                    NULL AS condition_grade,
                    selling_price,
                    quantity,
                    section
                FROM new_books
                WHERE quantity > 0

                UNION ALL

                SELECT
                    id,
                    "Second-hand" AS stock_type,
                    title,
                    author,
                    NULL AS isbn,
                    condition_grade,
                    selling_price,
                    1 AS quantity,
                    section
                FROM second_hand_copies
                WHERE status = "Available"
            ) AS available_books
            WHERE 1 = 1
        ';

        $parameters = [];

        if ($search !== '') {
            $sql .= '
                AND (
                    title LIKE :search
                    OR author LIKE :search
                    OR isbn LIKE :search
                )
            ';

            $parameters['search'] = '%' . $search . '%';
        }

        if ($section !== '') {
            $sql .= ' AND section = :section';

            $parameters['section'] = $section;
        }

        if (
            $stockType === 'New' ||
            $stockType === 'Second-hand'
        ) {
            $sql .= ' AND stock_type = :stock_type';

            $parameters['stock_type'] = $stockType;
        }

        if ($maximumPrice !== null) {
            $sql .= ' AND selling_price <= :maximum_price';

            $parameters['maximum_price'] = $maximumPrice;
        }

        $sql .= '
            ORDER BY title ASC, author ASC, stock_type ASC
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    /**
     * Return a list of available book sections.
     */
    public function getSections(): array
    {
        $statement = $this->pdo->query(
            '
            SELECT DISTINCT section
            FROM (
                SELECT section
                FROM new_books
                WHERE quantity > 0

                UNION

                SELECT section
                FROM second_hand_copies
                WHERE status = "Available"
            )
            WHERE section IS NOT NULL
              AND TRIM(section) != ""
            ORDER BY section ASC
            '
        );

        return $statement->fetchAll(
            PDO::FETCH_COLUMN
        );
    }
}