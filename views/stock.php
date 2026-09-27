<?php

use Bellbird\Support;

$newBooks = $stockRepository->getAllNewBooks();

?>
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
        <strong>The book was not saved.</strong>

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
        title. The available copies are managed using quantity.
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
                    value="<?= Support::escape(
                        $_POST['title'] ?? ''
                    ) ?>"
                    required
                >
            </label>

            <label>
                Author *
                <input
                    type="text"
                    name="author"
                    value="<?= Support::escape(
                        $_POST['author'] ?? ''
                    ) ?>"
                    required
                >
            </label>

            <label>
                ISBN
                <input
                    type="text"
                    name="isbn"
                    value="<?= Support::escape(
                        $_POST['isbn'] ?? ''
                    ) ?>"
                >
            </label>

            <label>
                Cost price
                <input
                    type="number"
                    name="cost_price"
                    min="0"
                    step="0.01"
                    value="<?= Support::escape(
                        $_POST['cost_price'] ?? ''
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
                    value="<?= Support::escape(
                        $_POST['selling_price'] ?? ''
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
                    value="<?= Support::escape(
                        $_POST['quantity'] ?? '0'
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
                    value="<?= Support::escape(
                        $_POST['section'] ?? ''
                    ) ?>"
                    required
                >
            </label>

            <label>
                Shelf location *
                <input
                    type="text"
                    name="shelf_location"
                    placeholder="For example: FIC-A2"
                    value="<?= Support::escape(
                        $_POST['shelf_location'] ?? ''
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
                                    (float) $book['selling_price'],
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
                                            (int) $book['quantity'] === 0
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