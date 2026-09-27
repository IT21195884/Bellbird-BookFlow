<?php

declare(strict_types=1);

namespace Bellbird;

use DateTimeImmutable;
use PDO;

final class CustomerOrderRepository
{
    public const ORDER_STATUSES = [
        'Unfulfilled',
        'Ordered',
        'Arrived',
        'Customer Notified',
        'Collected',
        'Cancelled',
        'Returned to Shelf',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /*
     * =====================================================
     * CUSTOMER FUNCTIONS — ORD-01
     * =====================================================
     */

    public function getCustomers(): array
    {
        return $this->pdo
            ->query(
                'SELECT *
                 FROM customers
                 ORDER BY name'
            )
            ->fetchAll();
    }

    public function findCustomer(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM customers
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $customer = $statement->fetch();

        return $customer === false
            ? null
            : $customer;
    }

    public function createCustomer(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO customers (
                name,
                phone,
                email,
                preferred_contact,
                notes
            ) VALUES (
                :name,
                :phone,
                :email,
                :preferred_contact,
                :notes
            )'
        );

        $statement->execute([
            'name' => trim($data['name']),

            'phone' => self::nullableText(
                $data['phone'] ?? null
            ),

            'email' => self::nullableText(
                $data['email'] ?? null
            ),

            'preferred_contact' =>
                $data['preferred_contact'],

            'notes' => self::nullableText(
                $data['notes'] ?? null
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public static function validateCustomer(
        array $data
    ): array {
        $errors = [];

        $name = trim(
            (string) ($data['name'] ?? '')
        );

        $phone = trim(
            (string) ($data['phone'] ?? '')
        );

        $email = trim(
            (string) ($data['email'] ?? '')
        );

        $preferredContact = trim(
            (string) (
                $data['preferred_contact'] ?? ''
            )
        );

        $allowedContactMethods = [
            'Call',
            'Text',
            'Email',
            'Customer will contact shop',
        ];

        /*
         * Customer name validation
         */
        if ($name === '') {
            $errors[] = 'Customer name is required.';
        } elseif (strlen($name) < 2) {
            $errors[] =
                'Customer name must contain at least two characters.';
        } elseif (strlen($name) > 100) {
            $errors[] =
                'Customer name cannot exceed 100 characters.';
        }

        /*
         * Phone validation
         */
        if ($phone !== '') {
            if (
                !preg_match(
                    '/^[0-9+()\s.-]+$/',
                    $phone
                )
            ) {
                $errors[] =
                    'Phone number can only contain numbers, spaces, +, brackets, dots or hyphens.';
            } else {
                $phoneDigits = preg_replace(
                    '/\D/',
                    '',
                    $phone
                );

                $digitCount = strlen(
                    (string) $phoneDigits
                );

                if (
                    $digitCount < 8 ||
                    $digitCount > 15
                ) {
                    $errors[] =
                        'Phone number must contain between 8 and 15 digits.';
                }
            }
        }

        /*
         * Email validation
         */
        if ($email !== '') {
            if (strlen($email) > 254) {
                $errors[] =
                    'Email address cannot exceed 254 characters.';
            } elseif (
                filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                ) === false
            ) {
                $errors[] =
                    'Enter a valid email address, for example customer@example.com.';
            }
        }

        /*
         * Preferred contact validation
         */
        if (
            !in_array(
                $preferredContact,
                $allowedContactMethods,
                true
            )
        ) {
            $errors[] =
                'Select a valid contact preference.';
        }

        if (
            in_array(
                $preferredContact,
                ['Call', 'Text'],
                true
            ) &&
            $phone === ''
        ) {
            $errors[] =
                'A phone number is required when the preferred contact method is Call or Text.';
        }

        if (
            $preferredContact === 'Email' &&
            $email === ''
        ) {
            $errors[] =
                'An email address is required when the preferred contact method is Email.';
        }

        return $errors;
    }

    /*
     * =====================================================
     * ORDER FUNCTIONS — ORD-02 TO ORD-08
     * =====================================================
     */

    public function findOrder(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT
                customer_orders.*,
                customers.name AS customer_name,
                customers.phone,
                customers.email,
                customers.preferred_contact
             FROM customer_orders
             JOIN customers
                ON customers.id =
                   customer_orders.customer_id
             WHERE customer_orders.id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $order = $statement->fetch();

        return $order === false
            ? null
            : $order;
    }

    public function searchOrders(
        string $term = '',
        bool $outstandingOnly = false
    ): array {
        $sql =
            'SELECT
                customer_orders.*,
                customers.name AS customer_name,
                customers.phone,
                customers.email,
                customers.preferred_contact
             FROM customer_orders
             JOIN customers
                ON customers.id =
                   customer_orders.customer_id
             WHERE (
                customers.name LIKE :customer_term
                OR customer_orders.book_title
                   LIKE :book_term
             )';

        if ($outstandingOnly) {
            $sql .=
                " AND customer_orders.status NOT IN (
                    'Collected',
                    'Cancelled',
                    'Returned to Shelf'
                )";
        }

        $sql .=
            ' ORDER BY
                customer_orders.order_date DESC,
                customer_orders.id DESC';

        $statement = $this->pdo->prepare($sql);

        $searchTerm = '%' . trim($term) . '%';

        $statement->execute([
            'customer_term' => $searchTerm,
            'book_term' => $searchTerm,
        ]);

        return $statement->fetchAll();
    }

    public function createOrder(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO customer_orders (
                customer_id,
                book_title,
                book_author,
                quantity,
                stock_preference,
                status,
                order_date,
                deposit_amount,
                notes
            ) VALUES (
                :customer_id,
                :book_title,
                :book_author,
                :quantity,
                :stock_preference,
                :status,
                :order_date,
                :deposit_amount,
                :notes
            )'
        );

        $statement->execute([
            'customer_id' =>
                (int) $data['customer_id'],

            'book_title' =>
                trim($data['book_title']),

            'book_author' => self::nullableText(
                $data['book_author'] ?? null
            ),

            'quantity' => (int) $data['quantity'],

            'stock_preference' =>
                $data['stock_preference'],

            'status' => 'Unfulfilled',

            'order_date' => $data['order_date'],

            'deposit_amount' =>
                self::nullableNumber(
                    $data['deposit_amount'] ?? null
                ),

            'notes' => self::nullableText(
                $data['notes'] ?? null
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateOrder(
        int $id,
        array $data
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE customer_orders
             SET
                customer_id = :customer_id,
                book_title = :book_title,
                book_author = :book_author,
                quantity = :quantity,
                stock_preference = :stock_preference,
                order_date = :order_date,
                deposit_amount = :deposit_amount,
                notes = :notes,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        return $statement->execute([
            'customer_id' =>
                (int) $data['customer_id'],

            'book_title' =>
                trim($data['book_title']),

            'book_author' => self::nullableText(
                $data['book_author'] ?? null
            ),

            'quantity' => (int) $data['quantity'],

            'stock_preference' =>
                $data['stock_preference'],

            'order_date' => $data['order_date'],

            'deposit_amount' =>
                self::nullableNumber(
                    $data['deposit_amount'] ?? null
                ),

            'notes' => self::nullableText(
                $data['notes'] ?? null
            ),

            'id' => $id,
        ]);
    }

    public function changeOrderStatus(
        int $id,
        string $status,
        string $eventDate
    ): bool {
        if (
            !in_array(
                $status,
                self::ORDER_STATUSES,
                true
            )
        ) {
            return false;
        }

        if (!self::validDate($eventDate)) {
            return false;
        }

        $dateField = match ($status) {
            'Arrived' => 'arrival_date',

            'Customer Notified' =>
                'notification_date',

            'Collected' => 'collection_date',

            default => null,
        };

        $sql =
            'UPDATE customer_orders
             SET
                status = :status,
                updated_at = CURRENT_TIMESTAMP';

        if ($dateField !== null) {
            $sql .= ", {$dateField} = :event_date";
        }

        $sql .= ' WHERE id = :id';

        $parameters = [
            'status' => $status,
            'id' => $id,
        ];

        if ($dateField !== null) {
            $parameters['event_date'] = $eventDate;
        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($parameters);

        return $statement->rowCount() === 1;
    }

    public function addContactAttempt(
        int $orderId,
        array $data
    ): bool {
        $statement = $this->pdo->prepare(
            'INSERT INTO contact_attempts (
                order_id,
                contact_date,
                method,
                outcome
            ) VALUES (
                :order_id,
                :contact_date,
                :method,
                :outcome
            )'
        );

        return $statement->execute([
            'order_id' => $orderId,

            'contact_date' =>
                $data['contact_date'],

            'method' => $data['method'],

            'outcome' => trim(
                $data['outcome']
            ),
        ]);
    }

    public function getContactAttempts(
        int $orderId
    ): array {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM contact_attempts
             WHERE order_id = :order_id
             ORDER BY contact_date DESC, id DESC'
        );

        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetchAll();
    }

    public function processUncollectedOrder(
        int $id,
        string $today
    ): bool {
        $order = $this->findOrder($id);

        if (
            $order === null ||
            $order['status'] !==
                'Customer Notified' ||
            empty($order['notification_date'])
        ) {
            return false;
        }

        $notificationDate =
            new DateTimeImmutable(
                $order['notification_date']
            );

        $returnDate =
            $notificationDate->modify('+14 days');

        $currentDate =
            new DateTimeImmutable($today);

        if ($currentDate < $returnDate) {
            return false;
        }

        $statement = $this->pdo->prepare(
            "UPDATE customer_orders
             SET
                status = 'Returned to Shelf',
                store_credit_amount =
                    COALESCE(deposit_amount, 0),
                updated_at = CURRENT_TIMESTAMP
             WHERE
                id = :id
                AND status = 'Customer Notified'"
        );

        $statement->execute([
            'id' => $id,
        ]);

        return $statement->rowCount() === 1;
    }

    public static function validateOrder(
        array $data
    ): array {
        $errors = [];

        if (
            filter_var(
                $data['customer_id'] ?? null,
                FILTER_VALIDATE_INT
            ) === false
        ) {
            $errors[] = 'Select a customer.';
        }

        if (
            trim(
                (string) ($data['book_title'] ?? '')
            ) === ''
        ) {
            $errors[] =
                'Requested book title is required.';
        }

        $quantity = filter_var(
            $data['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $quantity === false ||
            $quantity < 1
        ) {
            $errors[] =
                'Quantity must be at least one.';
        }

        if (
            !in_array(
                $data['stock_preference'] ?? '',
                ['New', 'Second-hand', 'Either'],
                true
            )
        ) {
            $errors[] =
                'Select a valid stock preference.';
        }

        if (
            !self::validDate(
                (string) (
                    $data['order_date'] ?? ''
                )
            )
        ) {
            $errors[] =
                'Enter a valid order date.';
        }

        if (
            ($data['deposit_amount'] ?? '') !== '' &&
            (
                !is_numeric(
                    $data['deposit_amount']
                ) ||
                (float) $data['deposit_amount'] < 0
            )
        ) {
            $errors[] =
                'Deposit must be zero or greater.';
        }

        return $errors;
    }

    public static function validateContactAttempt(
        array $data
    ): array {
        $errors = [];

        if (
            !self::validDate(
                (string) (
                    $data['contact_date'] ?? ''
                )
            )
        ) {
            $errors[] =
                'Enter a valid contact date.';
        }

        if (
            !in_array(
                $data['method'] ?? '',
                ['Call', 'Text', 'Email'],
                true
            )
        ) {
            $errors[] =
                'Select a valid contact method.';
        }

        if (
            trim(
                (string) ($data['outcome'] ?? '')
            ) === ''
        ) {
            $errors[] =
                'Contact outcome is required.';
        }

        return $errors;
    }

    /*
     * =====================================================
     * SHARED HELPERS
     * =====================================================
     */

    private static function validDate(
        string $date
    ): bool {
        $parsed =
            DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $date
            );

        return
            $parsed !== false &&
            $parsed->format('Y-m-d') === $date;
    }

    private static function nullableText(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

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