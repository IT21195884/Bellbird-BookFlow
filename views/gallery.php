<?php

use Bellbird\Support;

$search = trim(
    (string) ($_GET['search'] ?? '')
);

$section = trim(
    (string) ($_GET['section'] ?? '')
);

$stockType = trim(
    (string) ($_GET['stock_type'] ?? '')
);

$maximumPriceInput = trim(
    (string) ($_GET['maximum_price'] ?? '')
);

$maximumPrice = null;

if (
    $maximumPriceInput !== '' &&
    is_numeric($maximumPriceInput) &&
    (float) $maximumPriceInput >= 0
) {
    $maximumPrice =
        (float) $maximumPriceInput;
}

$books = $galleryRepository->searchBooks(
    $search,
    $section,
    $stockType,
    $maximumPrice
);

$sections = $galleryRepository->getSections();

?>

<section class="page-heading">
    <p class="eyebrow">
        Bellbird Books catalogue
    </p>

    <h1>Book Gallery</h1>

    <p>
        Browse available new and second-hand books.
        Search and filter the collection to find a
        suitable book.
    </p>

    <a
        class="button"
        href="index.php?page=cart"
    >
        View Cart (<?= $cart->count() ?>)
    </a>
</section>

<section class="panel">
    <h2>Find a book</h2>

    <form method="get">
        <input
            type="hidden"
            name="page"
            value="gallery"
        >

        <div class="gallery-filter-grid">
            <label>
                Title, author or ISBN

                <input
                    type="search"
                    name="search"
                    value="<?= Support::escape(
                        $search
                    ) ?>"
                    placeholder="Search books"
                >
            </label>

            <label>
                Section

                <select name="section">
                    <option value="">
                        All sections
                    </option>

                    <?php foreach (
                        $sections as $bookSection
                    ): ?>
                        <option
                            value="<?= Support::escape(
                                $bookSection
                            ) ?>"
                            <?= $section === $bookSection
                                ? 'selected'
                                : '' ?>
                        >
                            <?= Support::escape(
                                $bookSection
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Book type

                <select name="stock_type">
                    <option value="">
                        All book types
                    </option>

                    <option
                        value="New"
                        <?= $stockType === 'New'
                            ? 'selected'
                            : '' ?>
                    >
                        New books
                    </option>

                    <option
                        value="Second-hand"
                        <?= $stockType === 'Second-hand'
                            ? 'selected'
                            : '' ?>
                    >
                        Second-hand books
                    </option>
                </select>
            </label>

            <label>
                Maximum price

                <input
                    type="number"
                    name="maximum_price"
                    min="0"
                    step="0.01"
                    value="<?= Support::escape(
                        $maximumPriceInput
                    ) ?>"
                    placeholder="For example: 30.00"
                >
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Apply Filters
            </button>

            <a
                class="button button-secondary"
                href="index.php?page=gallery"
            >
                Clear Filters
            </a>
        </div>
    </form>
</section>

<section class="gallery-summary">
    <h2>Available Books</h2>

    <p>
        <?= count($books) ?>
        <?= count($books) === 1
            ? 'book'
            : 'books' ?>
        found
    </p>
</section>

<?php if ($books === []): ?>
    <section class="panel">
        <p class="empty-state">
            No available books matched your filters.
            Try changing or clearing the search options.
        </p>
    </section>
<?php else: ?>
    <section class="book-gallery">
        <?php foreach ($books as $book): ?>
            <?php
            $isbn = preg_replace(
                '/[^0-9Xx]/',
                '',
                (string) ($book['isbn'] ?? '')
            );

            $coverImage =
                $book['stock_type'] === 'New' &&
                $isbn !== ''
                    ? 'https://covers.openlibrary.org/b/isbn/'
                        . rawurlencode($isbn)
                        . '-M.jpg'
                    : 'assets/images/book-placeholder.svg';
            ?>

            <article class="book-card">
                <div class="book-cover-container">
                    <img
                        class="book-cover"
                        src="<?= Support::escape(
                            $coverImage
                        ) ?>"
                        alt="Cover for <?= Support::escape(
                            $book['title']
                        ) ?>"
                        onerror="this.onerror=null;this.src='assets/images/book-placeholder.svg';"
                    >
                </div>

                <div class="book-card-content">
                    <span class="stock-badge">
                        <?= Support::escape(
                            $book['stock_type']
                        ) ?>
                    </span>

                    <h3>
                        <?= Support::escape(
                            $book['title']
                        ) ?>
                    </h3>

                    <p class="book-author">
                        by
                        <?= Support::escape(
                            $book['author']
                        ) ?>
                    </p>

                    <dl class="book-information">
                        <div>
                            <dt>Section</dt>

                            <dd>
                                <?= Support::escape(
                                    $book['section']
                                ) ?>
                            </dd>
                        </div>

                        <?php if (
                            $book['stock_type'] ===
                            'Second-hand'
                        ): ?>
                            <div>
                                <dt>Condition</dt>

                                <dd>
                                    <?= Support::escape(
                                        $book[
                                            'condition_grade'
                                        ] ?? 'Not recorded'
                                    ) ?>
                                </dd>
                            </div>
                        <?php else: ?>
                            <div>
                                <dt>Available</dt>

                                <dd>
                                    <?= (int) $book[
                                        'quantity'
                                    ] ?>
                                </dd>
                            </div>
                        <?php endif; ?>
                    </dl>

                    <p class="book-price">
                        $<?= number_format(
                            (float) $book[
                                'selling_price'
                            ],
                            2
                        ) ?>
                    </p>

                    <form
                        method="post"
                        class="add-cart-form"
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
                            value="add_to_cart"
                        >

                        <input
                            type="hidden"
                            name="book_id"
                            value="<?= (int) $book['id'] ?>"
                        >

                        <input
                            type="hidden"
                            name="stock_type"
                            value="<?= Support::escape(
                                $book['stock_type']
                            ) ?>"
                        >

                        <?php if (
                            $book['stock_type'] === 'New'
                        ): ?>
                            <label>
                                Quantity

                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="<?= (int) $book[
                                        'quantity'
                                    ] ?>"
                                    value="1"
                                    required
                                >
                            </label>
                        <?php else: ?>
                            <input
                                type="hidden"
                                name="quantity"
                                value="1"
                            >

                            <p class="field-help">
                                One unique second-hand copy
                                is available.
                            </p>
                        <?php endif; ?>

                        <button type="submit">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>