<?php

use Bellbird\Support;

$bookId = filter_var(
    $_POST['book_id'] ?? $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
);

$book = null;

if ($bookId !== false) {
    $book = $stockRepository->findNewBook($bookId);
}

if ($book === null):
?>
    <section class="panel">
        <h1>Book not found</h1>

        <p>
            The selected new-book record could not be found.
        </p>

        <a class="button" href="index.php?page=stock">
            Return to stock
        </a>
    </section>
<?php
    return;
endif;

$value = function (string $field) use ($book): string {
    return Support::escape(
        $_POST[$field] ?? $book[$field] ?? ''
    );
};

?>
<section class="page-heading">
    <p class="eyebrow">STK-04</p>
    <h1>Update new-book record</h1>

    <p>
        Correct the selected book without affecting other
        stock records.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <strong>The book was not updated.</strong>

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
            value="update_new_book"
        >

        <input
            type="hidden"
            name="book_id"
            value="<?= Support::escape($book['id']) ?>"
        >

        <div class="form-grid">
            <label>
                Title *
                <input
                    type="text"
                    name="title"
                    value="<?= $value('title') ?>"
                    required
                >
            </label>

            <label>
                Author *
                <input
                    type="text"
                    name="author"
                    value="<?= $value('author') ?>"
                    required
                >
            </label>

            <label>
                ISBN
                <input
                    type="text"
                    name="isbn"
                    value="<?= $value('isbn') ?>"
                >
            </label>

            <label>
                Cost price
                <input
                    type="number"
                    name="cost_price"
                    min="0"
                    step="0.01"
                    value="<?= $value('cost_price') ?>"
                >
            </label>

            <label>
                Selling price *
                <input
                    type="number"
                    name="selling_price"
                    min="0"
                    step="0.01"
                    value="<?= $value('selling_price') ?>"
                    required
                >
            </label>

            <label>
                Quantity *
                <input
                    type="number"
                    name="quantity"
                    min="0"
                    step="1"
                    value="<?= $value('quantity') ?>"
                    required
                >
            </label>

            <label>
                Shop section *
                <input
                    type="text"
                    name="section"
                    value="<?= $value('section') ?>"
                    required
                >
            </label>

            <label>
                Shelf location *
                <input
                    type="text"
                    name="shelf_location"
                    value="<?= $value('shelf_location') ?>"
                    required
                >
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Update book
            </button>

            <a
                class="button button-secondary"
                href="index.php?page=stock"
            >
                Cancel
            </a>
        </div>
    </form>
</section>