<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Bellbird\Database;
use Bellbird\StockRepository;
use Bellbird\Support;

$pdo = Database::connect($config);

Database::initialise(
    $pdo,
    dirname(__DIR__) . '/database/schema.sql'
);

$stockRepository = new StockRepository($pdo);

$page = $_GET['page'] ?? 'dashboard';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Support::verifyCsrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'create_new_book') {
        $errors = StockRepository::validateNewBook(
            $_POST
        );

        if ($errors === []) {
            $stockRepository->createNewBook($_POST);

            Support::setMessage(
                'success',
                'New-book record created successfully.'
            );

            Support::redirect('stock');
        }

        $page = 'stock';
    } elseif ($action === 'adjust_new_quantity') {
        $bookId = filter_var(
            $_POST['book_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $change = filter_var(
            $_POST['change'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $bookId === false ||
            $change === false ||
            $change === 0
        ) {
            Support::setMessage(
                'error',
                'The stock adjustment was invalid.'
            );
        } elseif (
            !$stockRepository->adjustNewBookQuantity(
                $bookId,
                $change
            )
        ) {
            Support::setMessage(
                'error',
                'The quantity cannot be lower than zero.'
            );
        } else {
            Support::setMessage(
                'success',
                'Book quantity updated successfully.'
            );
        }

        Support::redirect('stock');
    } elseif ($action === 'update_new_book') {
        $bookId = filter_var(
            $_POST['book_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $errors = StockRepository::validateNewBook(
            $_POST
        );

        if ($bookId === false) {
            $errors[] = 'The selected book was invalid.';
        }

        if (
            $errors === [] &&
            $stockRepository->updateNewBook(
                $bookId,
                $_POST
            )
        ) {
            Support::setMessage(
                'success',
                'New-book record updated successfully.'
            );

            Support::redirect('stock');
        }

        if ($errors === []) {
            $errors[] =
                'The new-book record could not be updated.';
        }

        $page = 'edit-new-book';
    } elseif ($action === 'create_second_hand_copy') {
        $errors =
            StockRepository::validateSecondHandCopy(
                $_POST
            );

        if ($errors === []) {
            $stockRepository->createSecondHandCopy(
                $_POST
            );

            Support::setMessage(
                'success',
                'Second-hand copy recorded successfully.'
            );

            Support::redirect('stock');
        }

        $page = 'stock';
    } elseif ($action === 'update_second_hand_copy') {
        $copyId = filter_var(
            $_POST['copy_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $errors =
            StockRepository::validateSecondHandCopy(
                $_POST
            );

        if ($copyId === false) {
            $errors[] =
                'The selected second-hand copy was invalid.';
        }

        if (
            $errors === [] &&
            $stockRepository->updateSecondHandCopy(
                $copyId,
                $_POST
            )
        ) {
            Support::setMessage(
                'success',
                'Second-hand copy updated successfully.'
            );

            Support::redirect('stock');
        }

        if ($errors === []) {
            $errors[] =
                'The second-hand copy could not be updated.';
        }

        $page = 'edit-second-hand';
    } elseif (
        $action === 'mark_second_hand_unavailable'
    ) {
        $copyId = filter_var(
            $_POST['copy_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = (string) (
            $_POST['status'] ?? ''
        );

        if (
            $copyId === false ||
            !$stockRepository->markSecondHandUnavailable(
                $copyId,
                $status
            )
        ) {
            Support::setMessage(
                'error',
                'The second-hand copy could not be updated.'
            );
        } else {
            Support::setMessage(
                'success',
                'Second-hand copy marked as '
                . strtolower($status)
                . '.'
            );
        }

        Support::redirect('stock');
    }
}

$allowedPages = [
    'dashboard',
    'stock',
    'edit-new-book',
    'edit-second-hand',
    'customers',
    'orders',
];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    $page = '404';
}

$message = Support::getMessage();

require dirname(__DIR__) . '/views/header.php';

require dirname(__DIR__)
    . '/views/'
    . $page
    . '.php';

require dirname(__DIR__) . '/views/footer.php';