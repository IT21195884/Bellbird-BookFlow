<?php

use Bellbird\CustomerOrderRepository;
use Bellbird\Support;

$customers =
    $customerOrderRepository->getCustomers();

$searchTerm = trim(
    (string) ($_GET['q'] ?? '')
);

$outstandingOnly =
    ($_GET['outstanding'] ?? '') === '1';

$orders = $customerOrderRepository->searchOrders(
    $searchTerm,
    $outstandingOnly
);

$editOrderId = filter_var(
    $_GET['edit'] ??
    $_POST['order_id'] ??
    null,
    FILTER_VALIDATE_INT
);

$editingOrder = null;

if ($editOrderId !== false) {
    $editingOrder =
        $customerOrderRepository->findOrder(
            $editOrderId
        );
}

$orderValue = function (
    string $field,
    string $default = ''
) use ($editingOrder): string {
    return Support::escape(
        $_POST[$field]
        ?? $editingOrder[$field]
        ?? $default
    );
};

?>

<section class="page-heading">
    <p class="eyebrow">
        ORD-02 to ORD-08
    </p>

    <h1>Customer orders</h1>

    <p>
        Record, search and manage requested-book orders from
        customer request through collection.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= Support::escape($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="panel">
    <h2>Search customer orders</h2>

    <form method="get">
        <input
            type="hidden"
            name="page"
            value="orders"
        >

        <div class="search-grid">
            <label>
                Customer or requested book
                <input
                    type="search"
                    name="q"
                    value="<?= Support::escape(
                        $searchTerm
                    ) ?>"
                >
            </label>

            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="outstanding"
                    value="1"
                    <?= $outstandingOnly
                        ? 'checked'
                        : ''
                    ?>
                >
                Show outstanding orders only
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">Search orders</button>

            <a
                class="button button-secondary"
                href="index.php?page=orders"
            >
                Clear
            </a>
        </div>
    </form>
</section>

<section class="panel">
    <h2>
        <?= $editingOrder !== null
            ? 'Correct order #' .
                Support::escape($editingOrder['id'])
            : 'Record customer order'
        ?>
    </h2>

    <?php if ($customers === []): ?>
        <p class="empty-state">
            Create a customer before recording an order.
        </p>
    <?php else: ?>
        <form method="post">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= Support::escape(
                    Support::csrfToken()
                ) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="<?= $editingOrder !== null
                    ? 'update_order'
                    : 'create_order'
                ?>"
            >

            <?php if ($editingOrder !== null): ?>
                <input
                    type="hidden"
                    name="order_id"
                    value="<?= Support::escape(
                        $editingOrder['id']
                    ) ?>"
                >
            <?php endif; ?>

            <div class="form-grid">
                <label>
                    Customer *
                    <select name="customer_id" required>
                        <option value="">Select customer</option>

                        <?php foreach (
                            $customers as $customer
                        ): ?>
                            <option
                                value="<?= Support::escape(
                                    $customer['id']
                                ) ?>"
                                <?=
                                (string) (
                                    $_POST['customer_id']
                                    ?? $editingOrder[
                                        'customer_id'
                                    ]
                                    ?? ''
                                ) ===
                                (string) $customer['id']
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= Support::escape(
                                    $customer['name']
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Requested book title *
                    <input
                        name="book_title"
                        value="<?= $orderValue(
                            'book_title'
                        ) ?>"
                        required
                    >
                </label>

                <label>
                    Author
                    <input
                        name="book_author"
                        value="<?= $orderValue(
                            'book_author'
                        ) ?>"
                    >
                </label>

                <label>
                    Quantity *
                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        value="<?= $orderValue(
                            'quantity',
                            '1'
                        ) ?>"
                        required
                    >
                </label>

                <label>
                    Stock preference *
                    <select
                        name="stock_preference"
                        required
                    >
                        <?php foreach (
                            [
                                'Either',
                                'New',
                                'Second-hand',
                            ] as $preference
                        ): ?>
                            <option
                                <?=
                                (
                                    $_POST[
                                        'stock_preference'
                                    ]
                                    ?? $editingOrder[
                                        'stock_preference'
                                    ]
                                    ?? 'Either'
                                ) === $preference
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= Support::escape(
                                    $preference
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Order date *
                    <input
                        type="date"
                        name="order_date"
                        value="<?= $orderValue(
                            'order_date',
                            date('Y-m-d')
                        ) ?>"
                        required
                    >
                </label>

                <label>
                    Deposit amount
                    <input
                        type="number"
                        name="deposit_amount"
                        min="0"
                        step="0.01"
                        value="<?= $orderValue(
                            'deposit_amount'
                        ) ?>"
                    >
                </label>

                <label class="full-width">
                    Notes
                    <textarea
                        name="notes"
                        rows="3"
                    ><?= $orderValue('notes') ?></textarea>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit">
                    <?= $editingOrder !== null
                        ? 'Save corrections'
                        : 'Record order'
                    ?>
                </button>

                <?php if ($editingOrder !== null): ?>
                    <a
                        class="button button-secondary"
                        href="index.php?page=orders"
                    >
                        Cancel editing
                    </a>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</section>

<section class="panel">
    <h2>
        <?= $outstandingOnly
            ? 'Outstanding orders'
            : 'All orders'
        ?>
    </h2>

    <?php if ($orders === []): ?>
        <p class="empty-state">
            No matching customer orders.
        </p>
    <?php else: ?>
        <div class="order-list">
            <?php foreach ($orders as $order): ?>
                <?php
                $contactAttempts =
                    $customerOrderRepository
                        ->getContactAttempts(
                            (int) $order['id']
                        );

                $orderClosed = in_array(
                    $order['status'],
                    [
                        'Collected',
                        'Cancelled',
                        'Returned to Shelf',
                    ],
                    true
                );
                ?>

                <article class="order-card">
                    <div class="order-card-heading">
                        <div>
                            <p class="eyebrow">
                                Order
                                #<?= Support::escape(
                                    $order['id']
                                ) ?>
                            </p>

                            <h3>
                                <?= Support::escape(
                                    $order['book_title']
                                ) ?>
                            </h3>

                            <p>
                                Customer:
                                <strong>
                                    <?= Support::escape(
                                        $order[
                                            'customer_name'
                                        ]
                                    ) ?>
                                </strong>
                            </p>
                        </div>

                        <span class="status-badge">
                            <?= Support::escape(
                                $order['status']
                            ) ?>
                        </span>
                    </div>

                    <div class="order-details">
                        <p>
                            <strong>Author:</strong>
                            <?= Support::escape(
                                $order['book_author']
                                ?? 'Not specified'
                            ) ?>
                        </p>

                        <p>
                            <strong>Quantity:</strong>
                            <?= Support::escape(
                                $order['quantity']
                            ) ?>
                        </p>

                        <p>
                            <strong>Preference:</strong>
                            <?= Support::escape(
                                $order['stock_preference']
                            ) ?>
                        </p>

                        <p>
                            <strong>Order date:</strong>
                            <?= Support::escape(
                                $order['order_date']
                            ) ?>
                        </p>

                        <p>
                            <strong>Deposit:</strong>
                            $<?= number_format(
                                (float) (
                                    $order[
                                        'deposit_amount'
                                    ] ?? 0
                                ),
                                2
                            ) ?>
                        </p>

                        <p>
                            <strong>Contact:</strong>
                            <?= Support::escape(
                                $order[
                                    'preferred_contact'
                                ]
                            ) ?>
                        </p>
                    </div>

                    <div class="form-actions">
                        <a
                            class="button button-secondary"
                            href="index.php?page=orders&amp;edit=<?= Support::escape(
                                $order['id']
                            ) ?>"
                        >
                            Correct order
                        </a>
                    </div>

                    <?php if (!$orderClosed): ?>
                        <form
                            method="post"
                            class="compact-form"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= Support::escape(
                                    Support::csrfToken()
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="change_order_status"
                            >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= Support::escape(
                                    $order['id']
                                ) ?>"
                            >

                            <label>
                                New status
                                <select name="status">
                                    <?php foreach (
                                        CustomerOrderRepository
                                            ::ORDER_STATUSES
                                        as $status
                                    ): ?>
                                        <option
                                            <?=
                                            $status ===
                                            $order['status']
                                                ? 'selected'
                                                : ''
                                            ?>
                                        >
                                            <?= Support::escape(
                                                $status
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label>
                                Status date
                                <input
                                    type="date"
                                    name="event_date"
                                    value="<?= date(
                                        'Y-m-d'
                                    ) ?>"
                                >
                            </label>

                            <button type="submit">
                                Update status
                            </button>
                        </form>

                        <form
                            method="post"
                            class="compact-form"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= Support::escape(
                                    Support::csrfToken()
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="add_contact_attempt"
                            >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= Support::escape(
                                    $order['id']
                                ) ?>"
                            >

                            <label>
                                Contact date
                                <input
                                    type="date"
                                    name="contact_date"
                                    value="<?= date(
                                        'Y-m-d'
                                    ) ?>"
                                    required
                                >
                            </label>

                            <label>
                                Method
                                <select name="method">
                                    <option>Call</option>
                                    <option>Text</option>
                                    <option>Email</option>
                                </select>
                            </label>

                            <label>
                                Outcome
                                <input
                                    name="outcome"
                                    placeholder="No answer, message left..."
                                    required
                                >
                            </label>

                            <button type="submit">
                                Record contact
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (
                        $order['status'] ===
                        'Customer Notified'
                    ): ?>
                        <form method="post">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= Support::escape(
                                    Support::csrfToken()
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="process_uncollected"
                            >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= Support::escape(
                                    $order['id']
                                ) ?>"
                            >

                            <button
                                class="danger-button"
                                data-confirm="Return this uncollected order to the shelf?"
                            >
                                Process after 14 days
                            </button>
                        </form>
                    <?php endif; ?>

                    <details class="contact-history">
                        <summary>
                            Contact history
                            (<?= count($contactAttempts) ?>)
                        </summary>

                        <?php if ($contactAttempts === []): ?>
                            <p>No contact attempts recorded.</p>
                        <?php else: ?>
                            <ul>
                                <?php foreach (
                                    $contactAttempts as $attempt
                                ): ?>
                                    <li>
                                        <?= Support::escape(
                                            $attempt[
                                                'contact_date'
                                            ]
                                        ) ?>

                                        —

                                        <?= Support::escape(
                                            $attempt['method']
                                        ) ?>

                                        —

                                        <?= Support::escape(
                                            $attempt['outcome']
                                        ) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </details>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>