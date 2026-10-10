<?php

declare(strict_types=1);

namespace Bellbird;

use PDO;
use RuntimeException;
use Throwable;

final class CheckoutRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * Validate the demonstration checkout form.
     */
    public static function validateCheckout(
        array $data
    ): array {
        $errors = [];

        $name = trim(
            (string) ($data['customer_name'] ?? '')
        );

        $email = trim(
            (string) ($data['customer_email'] ?? '')
        );

        $phone = trim(
            (string) ($data['customer_phone'] ?? '')
        );

        $paymentResult = (string) (
            $data['payment_test_result'] ?? ''
        );

        if ($name === '') {
            $errors[] = 'Customer name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors[] =
                'Customer name must not exceed 100 characters.';
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

        if (
            $phone !== '' &&
            !preg_match(
                '/^[0-9+()\-\s]{8,20}$/',
                $phone
            )
        ) {
            $errors[] =
                'Enter a valid telephone number using 8 to 20 permitted characters.';
        }

        if (
            !in_array(
                $paymentResult,
                ['Approved', 'Declined'],
                true
            )
        ) {
            $errors[] =
                'Select a demonstration payment result.';
        }

        return $errors;
    }

    /**
     * Record an approved order and update inventory.
     */
    public function createApprovedOrder(
        array $customerData,
        array $cartItems
    ): string {
        if ($cartItems === []) {
            throw new RuntimeException(
                'The shopping cart is empty.'
            );
        }

        $orderReference =
            'BBF-'
            . date('Ymd')
            . '-'
            . strtoupper(
                bin2hex(random_bytes(3))
            );

        $total = 0.0;

        foreach ($cartItems as $item) {
            $total += (float) $item['subtotal'];
        }

        $this->pdo->beginTransaction();

        try {
            $orderStatement = $this->pdo->prepare(
                '
                INSERT INTO online_orders (
                    order_reference,
                    customer_name,
                    customer_email,
                    customer_phone,
                    fulfilment_method,
                    total_amount,
                    payment_status
                ) VALUES (
                    :order_reference,
                    :customer_name,
                    :customer_email,
                    :customer_phone,
                    :fulfilment_method,
                    :total_amount,
                    :payment_status
                )
                '
            );

            $orderStatement->execute([
                'order_reference' => $orderReference,
                'customer_name' => trim(
                    (string) $customerData[
                        'customer_name'
                    ]
                ),
                'customer_email' => strtolower(
                    trim(
                        (string) $customerData[
                            'customer_email'
                        ]
                    )
                ),
                'customer_phone' =>
                    self::nullableText(
                        $customerData[
                            'customer_phone'
                        ] ?? null
                    ),
                'fulfilment_method' => 'Store pickup',
                'total_amount' => $total,
                'payment_status' => 'Approved',
            ]);

            $onlineOrderId =
                (int) $this->pdo->lastInsertId();

            $itemStatement = $this->pdo->prepare(
                '
                INSERT INTO online_order_items (
                    online_order_id,
                    stock_type,
                    stock_id,
                    title,
                    author,
                    quantity,
                    unit_price,
                    line_total
                ) VALUES (
                    :online_order_id,
                    :stock_type,
                    :stock_id,
                    :title,
                    :author,
                    :quantity,
                    :unit_price,
                    :line_total
                )
                '
            );

            foreach ($cartItems as $item) {
                $stockType =
                    (string) $item['stock_type'];

                $stockId = (int) $item['id'];

                $quantity =
                    (int) $item['cart_quantity'];

                if ($stockType === 'New') {
                    $stockStatement =
                        $this->pdo->prepare(
                            '
                            UPDATE new_books
                            SET
                                quantity =
                                    quantity - :quantity,
                                updated_at =
                                    CURRENT_TIMESTAMP
                            WHERE id = :id
                              AND quantity >= :quantity
                            '
                        );

                    $stockStatement->execute([
                        'quantity' => $quantity,
                        'id' => $stockId,
                    ]);
                } elseif (
                    $stockType === 'Second-hand'
                ) {
                    $quantity = 1;

                    $stockStatement =
                        $this->pdo->prepare(
                            '
                            UPDATE second_hand_copies
                            SET
                                status = "Sold",
                                updated_at =
                                    CURRENT_TIMESTAMP
                            WHERE id = :id
                              AND status = "Available"
                            '
                        );

                    $stockStatement->execute([
                        'id' => $stockId,
                    ]);
                } else {
                    throw new RuntimeException(
                        'An invalid stock type was detected.'
                    );
                }

                if ($stockStatement->rowCount() !== 1) {
                    throw new RuntimeException(
                        'One or more books are no longer available in the requested quantity.'
                    );
                }

                $unitPrice =
                    (float) $item['selling_price'];

                $lineTotal =
                    $unitPrice * $quantity;

                $itemStatement->execute([
                    'online_order_id' => $onlineOrderId,
                    'stock_type' => $stockType,
                    'stock_id' => $stockId,
                    'title' => $item['title'],
                    'author' => $item['author'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $this->pdo->commit();

            return $orderReference;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Find an order using its receipt reference.
     */
    public function findOrderByReference(
        string $orderReference
    ): ?array {
        $statement = $this->pdo->prepare(
            '
            SELECT *
            FROM online_orders
            WHERE order_reference = :order_reference
            '
        );

        $statement->execute([
            'order_reference' => $orderReference,
        ]);

        $order = $statement->fetch();

        if ($order === false) {
            return null;
        }

        $itemStatement = $this->pdo->prepare(
            '
            SELECT *
            FROM online_order_items
            WHERE online_order_id = :online_order_id
            ORDER BY title ASC
            '
        );

        $itemStatement->execute([
            'online_order_id' => $order['id'],
        ]);

        $order['items'] =
            $itemStatement->fetchAll();

        return $order;
    }

    private static function nullableText(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}