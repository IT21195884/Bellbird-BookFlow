<?php

use Bellbird\Support;

$orderReference = (string) (
    $_SESSION['completed_order_reference'] ?? ''
);

$order = $orderReference !== ''
    ? $checkoutRepository->findOrderByReference(
        $orderReference
    )
    : null;

?>

<section class="page-heading">
    <p class="eyebrow">
        Payment approved
    </p>

    <h1>Order Confirmed</h1>

    <p>
        The transaction was approved and
        the order has been recorded successfully.
    </p>
</section>

<?php if ($order === null): ?>
    <section class="panel">
        <p>
            No completed order receipt is currently
            available.
        </p>

        <a
            class="button"
            href="index.php?page=gallery"
        >
            Return to Gallery
        </a>
    </section>
<?php else: ?>
    <section class="panel receipt">
        <div class="receipt-heading">
            <div>
                <h2>Bellbird Books Receipt</h2>

                <p>
                    Reference:
                    <strong>
                        <?= Support::escape(
                            $order['order_reference']
                        ) ?>
                    </strong>
                </p>
            </div>

            <span class="payment-approved">
                Approved
            </span>
        </div>

        <div class="order-customer-details">
            <p>
                <strong>Customer:</strong>
                <?= Support::escape(
                    $order['customer_name']
                ) ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?= Support::escape(
                    $order['customer_email']
                ) ?>
            </p>

            <p>
                <strong>Collection:</strong>
                <?= Support::escape(
                    $order['fulfilment_method']
                ) ?>
            </p>

            <p>
                <strong>Order date:</strong>
                <?= Support::escape(
                    $order['created_at']
                ) ?>
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Unit price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach (
                        $order['items'] as $item
                    ): ?>
                        <tr>
                            <td>
                                <?= Support::escape(
                                    $item['title']
                                ) ?>

                                <br>

                                <small>
                                    <?= Support::escape(
                                        $item['author']
                                    ) ?>
                                </small>
                            </td>

                            <td>
                                <?= Support::escape(
                                    $item['stock_type']
                                ) ?>
                            </td>

                            <td>
                                <?= (int) $item['quantity'] ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float) $item[
                                        'unit_price'
                                    ],
                                    2
                                ) ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float) $item[
                                        'line_total'
                                    ],
                                    2
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-total">
            Total paid:
            <strong>
                $<?= number_format(
                    (float) $order['total_amount'],
                    2
                ) ?>
            </strong>
        </div>

        <p class="demo-receipt-note">
            Payment Successful !
        </p>

        <a
            class="button"
            href="index.php?page=gallery"
        >
            Continue Browsing
        </a>
    </section>
<?php endif; ?>