<?php

use Bellbird\Support;

$cartItems =
    $cart->getItems($galleryRepository);

$cartTotal = $cart->total($cartItems);

?>

<section class="page-heading">
    <p class="eyebrow">
        Payment portal
    </p>

    <h1>Checkout</h1>

    <p>
        Review your order and complete
        checkout.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= Support::escape($error) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($cartItems === []): ?>
    <section class="panel">
        <p class="empty-state">
            Your cart is empty. Add at least one book
            before proceeding to checkout.
        </p>

        <a
            class="button"
            href="index.php?page=gallery"
        >
            Browse Books
        </a>
    </section>
<?php else: ?>
    <div class="checkout-layout">
        <section class="panel">
            <h2>Customer Details</h2>

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
                    value="process_checkout"
                >

                <div class="form-grid">
                    <label>
                        Customer name *

                        <input
                            name="customer_name"
                            maxlength="100"
                            value="<?= Support::escape(
                                $_POST[
                                    'customer_name'
                                ] ?? ''
                            ) ?>"
                            required
                        >
                    </label>

                    <label>
                        Email address *

                        <input
                            type="email"
                            name="customer_email"
                            maxlength="150"
                            value="<?= Support::escape(
                                $_POST[
                                    'customer_email'
                                ] ?? ''
                            ) ?>"
                            required
                        >
                    </label>

                    <label>
                        Telephone number

                        <input
                            type="tel"
                            name="customer_phone"
                            maxlength="20"
                            pattern="[0-9+()\-\s]{8,20}"
                            value="<?= Support::escape(
                                $_POST[
                                    'customer_phone'
                                ] ?? ''
                            ) ?>"
                        >

                        <span class="field-help">
                            Use 8–20 characters, including
                            numbers, spaces, +, -, or brackets.
                        </span>
                    </label>

                    <label>
                        Collection method

                        <input
                            value="Store pickup"
                            disabled
                        >
                    </label>

                    <label class="full-width">
                         Payment result *

                        <select
                            name="payment_test_result"
                            required
                        >
                            <option value="">
                                Select result
                            </option>

                            <option
                                value="Approved"
                                <?= (
                                    $_POST[
                                        'payment_test_result'
                                    ] ?? ''
                                ) === 'Approved'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Approved transaction
                            </option>

                            <option
                                value="Declined"
                                <?= (
                                    $_POST[
                                        'payment_test_result'
                                    ] ?? ''
                                ) === 'Declined'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Declined transaction
                            </option>
                        </select>
                    </label>
                </div>

                <div class="demo-payment-warning">
                    <strong>
                        Check weather before proceeding:
                    </strong>

                    American Express not accepted.
                </div>

                <div class="form-actions">
                    <button type="submit">
                        Complete Payment
                    </button>

                    <a
                        class="button button-secondary"
                        href="index.php?page=cart"
                    >
                        Return to Cart
                    </a>
                </div>
            </form>
        </section>

        <aside class="panel checkout-summary">
            <h2>Order Summary</h2>

            <?php foreach ($cartItems as $item): ?>
                <div class="checkout-summary-item">
                    <div>
                        <strong>
                            <?= Support::escape(
                                $item['title']
                            ) ?>
                        </strong>

                        <small>
                            <?= Support::escape(
                                $item['stock_type']
                            ) ?>
                            ×
                            <?= (int) $item[
                                'cart_quantity'
                            ] ?>
                        </small>
                    </div>

                    <span>
                        $<?= number_format(
                            (float) $item['subtotal'],
                            2
                        ) ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <div class="checkout-total">
                <span>Total</span>

                <strong>
                    $<?= number_format(
                        $cartTotal,
                        2
                    ) ?>
                </strong>
            </div>
        </aside>
    </div>
<?php endif; ?>