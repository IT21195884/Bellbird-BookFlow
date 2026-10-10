<?php

use Bellbird\Support;

$cartItems =
    $cart->getItems($galleryRepository);

$cartTotal = $cart->total($cartItems);

?>

<section class="page-heading">
    <p class="eyebrow">Customer shopping cart</p>

    <h1>Your Cart</h1>

    <p>
        Review the books and quantities before proceeding
        to the payment stage.
    </p>
</section>

<?php if ($cartItems === []): ?>
    <section class="panel">
        <p class="empty-state">
            Your cart is currently empty.
        </p>

        <a
            class="button"
            href="index.php?page=gallery"
        >
            Browse Book Gallery
        </a>
    </section>
<?php else: ?>
    <section class="panel">
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
                value="update_cart"
            >

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Remove</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (
                            $cartItems as $item
                        ): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= Support::escape(
                                            $item['title']
                                        ) ?>
                                    </strong>

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

                                    <?php if (
                                        $item[
                                            'condition_grade'
                                        ] !== null
                                    ): ?>
                                        <br>

                                        <small>
                                            <?= Support::escape(
                                                $item[
                                                    'condition_grade'
                                                ]
                                            ) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float) $item[
                                            'selling_price'
                                        ],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <input
                                        class="cart-quantity"
                                        type="number"
                                        name="quantities[<?= Support::escape(
                                            $item['cart_key']
                                        ) ?>]"
                                        min="0"
                                        max="<?= (int) $item[
                                            'quantity'
                                        ] ?>"
                                        value="<?= (int) $item[
                                            'cart_quantity'
                                        ] ?>"
                                    >
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float) $item[
                                            'subtotal'
                                        ],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <button
                                        type="submit"
                                        name="remove_key"
                                        value="<?= Support::escape(
                                            $item['cart_key']
                                        ) ?>"
                                        formaction="index.php"
                                        formmethod="post"
                                        class="button-danger"
                                        onclick="return false;"
                                        disabled
                                    >
                                        Use button below
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-actions">
                <button type="submit">
                    Update Cart
                </button>
            </div>
        </form>

        <div class="cart-total">
            <span>Total</span>

            <strong>
                $<?= number_format($cartTotal, 2) ?>
            </strong>
        </div>
    </section>

    <section class="cart-bottom-actions">
        <a
            class="button button-secondary"
            href="index.php?page=gallery"
        >
            Continue Shopping
        </a>

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
                value="clear_cart"
            >

            <button
                type="submit"
                class="button-danger"
            >
                Clear Cart
            </button>
        </form>
    </section>

    <section class="panel checkout-notice">
        <h2>Ready to Checkout?</h2>

        <p>
            Continue to the payment portal.
            Card or Banking details are required.
        </p>

        <a
            class="button"
            href="index.php?page=checkout"
        >
            Proceed to Checkout
        </a>
    </section>

    <section class="panel">
        <h2>Remove an individual book</h2>

        <div class="remove-item-list">
            <?php foreach ($cartItems as $item): ?>
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
                        value="remove_from_cart"
                    >

                    <input
                        type="hidden"
                        name="cart_key"
                        value="<?= Support::escape(
                            $item['cart_key']
                        ) ?>"
                    >

                    <span>
                        <?= Support::escape(
                            $item['title']
                        ) ?>
                    </span>

                    <button
                        type="submit"
                        class="button-danger"
                    >
                        Remove
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>