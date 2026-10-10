<?php

declare(strict_types=1);

namespace Bellbird;

final class Cart
{
    private const SESSION_KEY = 'shopping_cart';

    public function __construct()
    {
        if (
            !isset($_SESSION[self::SESSION_KEY]) ||
            !is_array($_SESSION[self::SESSION_KEY])
        ) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Add an available book to the session cart.
     */
    public function add(
        array $book,
        int $requestedQuantity = 1
    ): void {
        $stockType = (string) $book['stock_type'];
        $bookId = (int) $book['id'];

        $key = $this->createKey(
            $stockType,
            $bookId
        );

        $maximumQuantity =
            $stockType === 'Second-hand'
                ? 1
                : (int) $book['quantity'];

        $currentQuantity =
            (int) (
                $_SESSION[self::SESSION_KEY][$key]
                ?? 0
            );

        $newQuantity =
            $currentQuantity +
            max(1, $requestedQuantity);

        $_SESSION[self::SESSION_KEY][$key] =
            min($newQuantity, $maximumQuantity);
    }

    /**
     * Change the quantity of one cart item.
     */
    public function update(
        string $key,
        int $quantity,
        GalleryRepository $galleryRepository
    ): bool {
        $identity = $this->parseKey($key);

        if ($identity === null) {
            return false;
        }

        if ($quantity <= 0) {
            $this->remove($key);

            return true;
        }

        $book =
            $galleryRepository->findAvailableBook(
                $identity['stock_type'],
                $identity['id']
            );

        if ($book === null) {
            $this->remove($key);

            return false;
        }

        $maximumQuantity =
            $identity['stock_type'] === 'Second-hand'
                ? 1
                : (int) $book['quantity'];

        $_SESSION[self::SESSION_KEY][$key] =
            min($quantity, $maximumQuantity);

        return true;
    }

    /**
     * Remove one item from the cart.
     */
    public function remove(string $key): void
    {
        unset($_SESSION[self::SESSION_KEY][$key]);
    }

    /**
     * Remove all cart items.
     */
    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    /**
     * Return validated cart items with totals.
     */
    public function getItems(
        GalleryRepository $galleryRepository
    ): array {
        $items = [];

        foreach (
            $_SESSION[self::SESSION_KEY]
            as $key => $quantity
        ) {
            $identity = $this->parseKey(
                (string) $key
            );

            if ($identity === null) {
                unset(
                    $_SESSION[self::SESSION_KEY][$key]
                );

                continue;
            }

            $book =
                $galleryRepository->findAvailableBook(
                    $identity['stock_type'],
                    $identity['id']
                );

            if ($book === null) {
                unset(
                    $_SESSION[self::SESSION_KEY][$key]
                );

                continue;
            }

            $maximumQuantity =
                $identity['stock_type'] ===
                'Second-hand'
                    ? 1
                    : (int) $book['quantity'];

            $validatedQuantity = min(
                max(1, (int) $quantity),
                $maximumQuantity
            );

            $_SESSION[self::SESSION_KEY][$key] =
                $validatedQuantity;

            $book['cart_key'] = $key;
            $book['cart_quantity'] =
                $validatedQuantity;
            $book['subtotal'] =
                (float) $book['selling_price'] *
                $validatedQuantity;

            $items[] = $book;
        }

        return $items;
    }

    /**
     * Return the total number of books in the cart.
     */
    public function count(): int
    {
        return array_sum(
            array_map(
                'intval',
                $_SESSION[self::SESSION_KEY]
            )
        );
    }

    /**
     * Calculate the total price.
     */
    public function total(array $items): float
    {
        return array_sum(
            array_column($items, 'subtotal')
        );
    }

    private function createKey(
        string $stockType,
        int $id
    ): string {
        return $stockType . ':' . $id;
    }

    private function parseKey(
        string $key
    ): ?array {
        $parts = explode(':', $key, 2);

        if (count($parts) !== 2) {
            return null;
        }

        $stockType = $parts[0];

        if (
            $stockType !== 'New' &&
            $stockType !== 'Second-hand'
        ) {
            return null;
        }

        $id = filter_var(
            $parts[1],
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id < 1) {
            return null;
        }

        return [
            'stock_type' => $stockType,
            'id' => $id,
        ];
    }
}