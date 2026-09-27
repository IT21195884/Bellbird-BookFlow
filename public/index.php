<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Bellbird\CustomerOrderRepository;
use Bellbird\Database;
use Bellbird\StockRepository;
use Bellbird\Support;

$pdo = Database::connect($config);

Database::initialise(
    $pdo,
    dirname(__DIR__) . '/database/schema.sql'
);

$stockRepository = new StockRepository($pdo);

$customerOrderRepository =
    new CustomerOrderRepository($pdo);

$page = $_GET['page'] ?? 'dashboard';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Support::verifyCsrf();

    $action = $_POST['action'] ?? '';

    /*
     * STOCK ACTIONS
     */

    if ($action === 'create_new_book') {
        $errors =
            StockRepository::validateNewBook($_POST);

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
            $change === 0 ||
            !$stockRepository->adjustNewBookQuantity(
                $bookId,
                $change
            )
        ) {
            Support::setMessage(
                'error',
                'The quantity adjustment was invalid.'
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

        $errors =
            StockRepository::validateNewBook($_POST);

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

        $page = 'edit-new-book';
    } elseif (
        $action === 'create_second_hand_copy'
    ) {
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
    } elseif (
        $action === 'update_second_hand_copy'
    ) {
        $copyId = filter_var(
            $_POST['copy_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $errors =
            StockRepository::validateSecondHandCopy(
                $_POST
            );

        if ($copyId === false) {
            $errors[] = 'The selected copy was invalid.';
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
                'The copy could not be updated.'
            );
        } else {
            Support::setMessage(
                'success',
                'Second-hand copy updated successfully.'
            );
        }

        Support::redirect('stock');
    }

    /*
     * CUSTOMER AND ORDER ACTIONS
     */

    elseif ($action === 'create_customer') {
        $errors =
            CustomerOrderRepository::validateCustomer(
                $_POST
            );

        if ($errors === []) {
            $customerOrderRepository->createCustomer(
                $_POST
            );

            Support::setMessage(
                'success',
                'Customer record created successfully.'
            );

            Support::redirect('customers');
        }

        $page = 'customers';
    } elseif ($action === 'create_order') {
        $errors =
            CustomerOrderRepository::validateOrder(
                $_POST
            );

        if ($errors === []) {
            $customerOrderRepository->createOrder(
                $_POST
            );

            Support::setMessage(
                'success',
                'Customer order recorded successfully.'
            );

            Support::redirect('orders');
        }

        $page = 'orders';
    } elseif ($action === 'update_order') {
        $orderId = filter_var(
            $_POST['order_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $errors =
            CustomerOrderRepository::validateOrder(
                $_POST
            );

        if ($orderId === false) {
            $errors[] = 'The selected order was invalid.';
        }

        if (
            $errors === [] &&
            $customerOrderRepository->updateOrder(
                $orderId,
                $_POST
            )
        ) {
            Support::setMessage(
                'success',
                'Customer order corrected successfully.'
            );

            Support::redirect('orders');
        }

        $page = 'orders';
    } elseif ($action === 'change_order_status') {
        $orderId = filter_var(
            $_POST['order_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = (string) (
            $_POST['status'] ?? ''
        );

        $eventDate = (string) (
            $_POST['event_date']
            ?? date('Y-m-d')
        );

        if (
            $orderId === false ||
            !$customerOrderRepository
                ->changeOrderStatus(
                    $orderId,
                    $status,
                    $eventDate
                )
        ) {
            Support::setMessage(
                'error',
                'The order status could not be changed.'
            );
        } else {
            Support::setMessage(
                'success',
                "Order status changed to {$status}."
            );
        }

        Support::redirect('orders');
    } elseif ($action === 'add_contact_attempt') {
        $orderId = filter_var(
            $_POST['order_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $errors =
            CustomerOrderRepository
                ::validateContactAttempt($_POST);

        if (
            $orderId === false ||
            $errors !== [] ||
            !$customerOrderRepository
                ->addContactAttempt(
                    $orderId,
                    $_POST
                )
        ) {
            Support::setMessage(
                'error',
                $errors[0]
                    ?? 'Contact attempt could not be saved.'
            );
        } else {
            Support::setMessage(
                'success',
                'Contact attempt recorded successfully.'
            );
        }

        Support::redirect('orders');
    } elseif ($action === 'process_uncollected') {
        $orderId = filter_var(
            $_POST['order_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $orderId === false ||
            !$customerOrderRepository
                ->processUncollectedOrder(
                    $orderId,
                    date('Y-m-d')
                )
        ) {
            Support::setMessage(
                'error',
                'Only orders notified at least 14 days ago can be returned to the shelf.'
            );
        } else {
            Support::setMessage(
                'success',
                'Order returned to shelf and deposit converted to store credit.'
            );
        }

        Support::redirect('orders');
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