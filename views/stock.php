<?php

use Bellbird\Support;

$newBooks = $stockRepository->getAllNewBooks();

$secondHandCopies =
    $stockRepository->getAvailableSecondHandCopies();

$submittedAction = $_POST['action'] ?? '';

$newBookValue = function (
    string $field,
    string $default = ''
) use ($submittedAction): string {
    if ($submittedAction !== 'create_new_book') {
        return Support::escape($default);
    }

    return Support::escape(
        $_POST[$field] ?? $default
    );
};

$secondHandValue = function (
    string $field,
    string $default = ''
) use ($submittedAction): string {
    if ($submittedAction !== 'create_second_hand_copy') {
        return Support::escape($default);
    }

    return Support::escape(
        $_POST[$field] ?? $default
    );
};

?>

<!-- ======================================================
     NEW-BOOK STOCK
     ====================================================== -->

<section class="page-heading">
    <p class="eyebrow">Stock management</p>

    <h1>New-book stock</h1>

    <p>
        Record new titles, maintain quantities and update
        existing new-book information.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <strong>The stock record was not saved.</strong>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= Support::escape($error) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="panel">
    <h2>Add a new-book title</h2>

    <p class="help-text">
        Use one record for identical new copies of the same
        title. Available copies are managed using quantity.
    </p>

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
            value="create_new_book"
        >

        <div class="form-grid">
            <label>
                Title *
                <input
                    type="text"
                    name="title"
                    value="<?= $newBookValue('title') ?>"
                    required
                >
            </label>

            <label>
                Author *
                <input
                    type="text"
                    name="author"
                    value="<?= $newBookValue('author') ?>"
                    required
                >
            </label>

            <label>
                ISBN
                <input
                    type="text"
                    name="isbn"
                    value="<?= $newBookValue('isbn') ?>"
                >
            </label>

            <label>
                Cost price
                <input
                    type="number"
                    name="cost_price"
                    min="0"
                    step="0.01"
                    value="<?= $newBookValue(
                        'cost_price'
                    ) ?>"
                >
            </label>

            <label>
                Selling price *
                <input
                    type="number"
                    name="selling_price"
                    min="0"
                    step="0.01"
                    value="<?= $newBookValue(
                        'selling_price'
                    ) ?>"
                    required
                >
            </label>

            <label>
                Starting quantity *
                <input
                    type="number"
                    name="quantity"
                    min="0"
                    step="1"
                    value="<?= $newBookValue(
                        'quantity',
                        '0'
                    ) ?>"
                    required
                >
            </label>

            <label>
                Shop section *
                <input
                    type="text"
                    name="section"
                    placeholder="For example: Fiction"
                    value="<?= $newBookValue('section') ?>"
                    required
                >
            </label>

            <label>
                Shelf location *
                <input
                    type="text"
                    name="shelf_location"
                    placeholder="For example: FIC-A2"
                    value="<?= $newBookValue(
                        'shelf_location'
                    ) ?>"
                    required
                >
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Save new book
            </button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>Recorded new books</h2>

    <?php if ($newBooks === []): ?>
        <p class="empty-state">
            No new-book titles have been recorded.
        </p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title and author</th>
                        <th>ISBN</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Stock actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($newBooks as $book): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= Support::escape(
                                        $book['title']
                                    ) ?>
                                </strong>

                                <br>

                                <span class="secondary-text">
                                    <?= Support::escape(
                                        $book['author']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= Support::escape(
                                    $book['isbn'] ?? '—'
                                ) ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float) $book[
                                        'selling_price'
                                    ],
                                    2
                                ) ?>
                            </td>

                            <td>
                                <span class="quantity">
                                    <?= Support::escape(
                                        $book['quantity']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= Support::escape(
                                    $book['section']
                                ) ?>

                                <br>

                                <span class="secondary-text">
                                    <?= Support::escape(
                                        $book['shelf_location']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <div class="stock-actions">
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
                                            value="adjust_new_quantity"
                                        >

                                        <input
                                            type="hidden"
                                            name="book_id"
                                            value="<?= Support::escape(
                                                $book['id']
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="change"
                                            value="-1"
                                            class="small-button"
                                            <?=
                                            (int) $book['quantity']
                                                === 0
                                                ? 'disabled'
                                                : ''
                                            ?>
                                        >
                                            −1
                                        </button>

                                        <button
                                            type="submit"
                                            name="change"
                                            value="1"
                                            class="small-button"
                                        >
                                            +1
                                        </button>
                                    </form>

                                    <a
                                        class="text-link"
                                        href="index.php?page=edit-new-book&amp;id=<?= Support::escape(
                                            $book['id']
                                        ) ?>"
                                    >
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- ======================================================
     SECOND-HAND STOCK
     ====================================================== -->

<section class="section-divider">
    <p class="eyebrow">Individual copy management</p>

    <h1>Second-hand stock</h1>

    <p>
        Every second-hand copy is recorded separately because
        its condition, price and location may be different.
    </p>
</section>

<section class="panel">
    <h2>Record a second-hand copy</h2>

    <p class="help-text">
        This form creates one record for one physical copy.
        Do not combine second-hand copies into a quantity.
    </p>

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
            value="create_second_hand_copy"
        >

        <div class="form-grid">
            <label>
                Title *
                <input
                    type="text"
                    name="title"
                    value="<?= $secondHandValue('title') ?>"
                    required
                >
            </label>

            <label>
                Author *
                <input
                    type="text"
                    name="author"
                    value="<?= $secondHandValue('author') ?>"
                    required
                >
            </label>

            <label>
                Condition *
                <select
                    name="condition_grade"
                    required
                >
                    <option value="">
                        Select condition
                    </option>

                    <?php
                    $conditions = [
                        'As New',
                        'Very Good',
                        'Good',
                        'Fair',
                        'Reading Copy',
                    ];

                    foreach ($conditions as $condition):
                    ?>
                        <option
                            value="<?= Support::escape(
                                $condition
                            ) ?>"
                            <?=
                            (
                                $submittedAction
                                === 'create_second_hand_copy'
                                &&
                                ($_POST['condition_grade'] ?? '')
                                === $condition
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            <?= Support::escape(
                                $condition
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Purchase price
                <input
                    type="number"
                    name="purchase_price"
                    min="0"
                    step="0.01"
                    value="<?= $secondHandValue(
                        'purchase_price'
                    ) ?>"
                >
            </label>

            <label>
                Selling price *
                <input
                    type="number"
                    name="selling_price"
                    min="0"
                    step="0.01"
                    value="<?= $secondHandValue(
                        'selling_price'
                    ) ?>"
                    required
                >
            </label>

            <label>
                Shop section *
                <input
                    type="text"
                    name="section"
                    value="<?= $secondHandValue(
                        'section',
                        'Second-hand'
                    ) ?>"
                    required
                >
            </label>

            <label>
                Shelf location *
                <input
                    type="text"
                    name="shelf_location"
                    placeholder="For example: SH-B3"
                    value="<?= $secondHandValue(
                        'shelf_location'
                    ) ?>"
                    required
                >
            </label>

            <label>
                Intake reference
                <input
                    type="text"
                    name="intake_reference"
                    placeholder="For example: BB-2026-001"
                    value="<?= $secondHandValue(
                        'intake_reference'
                    ) ?>"
                >
            </label>

            <label>
                Intake date
                <input
                    type="date"
                    name="intake_date"
                    value="<?= $secondHandValue(
                        'intake_date'
                    ) ?>"
                >
            </label>

            <label>
                Acquisition source
                <input
                    type="text"
                    name="acquisition_source"
                    placeholder="Customer trade-in"
                    value="<?= $secondHandValue(
                        'acquisition_source'
                    ) ?>"
                >
            </label>

            <label class="full-width">
                Intake notes
                <textarea
                    name="notes"
                    rows="3"
                    placeholder="Damage, markings or other details"
                ><?= $secondHandValue('notes') ?></textarea>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Save second-hand copy
            </button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>Available second-hand copies</h2>

    <?php if ($secondHandCopies === []): ?>
        <p class="empty-state">
            No available second-hand copies have been recorded.
        </p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Copy</th>
                        <th>Book</th>
                        <th>Condition</th>
                        <th>Prices</th>
                        <th>Location</th>
                        <th>Intake details</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach (
                        $secondHandCopies as $copy
                    ): ?>
                        <tr>
                            <td>
                                #<?= Support::escape(
                                    $copy['id']
                                ) ?>
                            </td>

                            <td>
                                <strong>
                                    <?= Support::escape(
                                        $copy['title']
                                    ) ?>
                                </strong>

                                <br>

                                <span class="secondary-text">
                                    <?= Support::escape(
                                        $copy['author']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <span class="status-badge">
                                    <?= Support::escape(
                                        $copy['condition_grade']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                Selling:
                                $<?= number_format(
                                    (float) $copy[
                                        'selling_price'
                                    ],
                                    2
                                ) ?>

                                <br>

                                <span class="secondary-text">
                                    Purchase:

                                    <?php if (
                                        $copy['purchase_price']
                                        === null
                                    ): ?>
                                        —
                                    <?php else: ?>
                                        $<?= number_format(
                                            (float) $copy[
                                                'purchase_price'
                                            ],
                                            2
                                        ) ?>
                                    <?php endif; ?>
                                </span>
                            </td>

                            <td>
                                <?= Support::escape(
                                    $copy['section']
                                ) ?>

                                <br>

                                <span class="secondary-text">
                                    <?= Support::escape(
                                        $copy['shelf_location']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                Reference:
                                <?= Support::escape(
                                    $copy['intake_reference']
                                    ?? '—'
                                ) ?>

                                <br>

                                Date:
                                <?= Support::escape(
                                    $copy['intake_date']
                                    ?? '—'
                                ) ?>

                                <br>

                                Source:
                                <?= Support::escape(
                                    $copy['acquisition_source']
                                    ?? '—'
                                ) ?>
                            </td>

                            <td>
                                <div class="copy-actions">
                                    <a
                                        class="text-link"
                                        href="index.php?page=edit-second-hand&amp;id=<?= Support::escape(
                                            $copy['id']
                                        ) ?>"
                                    >
                                        Edit
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
                                            value="mark_second_hand_unavailable"
                                        >

                                        <input
                                            type="hidden"
                                            name="copy_id"
                                            value="<?= Support::escape(
                                                $copy['id']
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="Sold"
                                        >

                                        <button
                                            type="submit"
                                            class="small-button"
                                            data-confirm="Mark this individual copy as sold?"
                                        >
                                            Mark sold
                                        </button>
                                    </form>

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
                                            value="mark_second_hand_unavailable"
                                        >

                                        <input
                                            type="hidden"
                                            name="copy_id"
                                            value="<?= Support::escape(
                                                $copy['id']
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="Removed"
                                        >

                                        <button
                                            type="submit"
                                            class="small-button danger-button"
                                            data-confirm="Remove this copy from available stock?"
                                        >
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>