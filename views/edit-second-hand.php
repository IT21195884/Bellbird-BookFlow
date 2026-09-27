<?php

use Bellbird\Support;

$copyId = filter_var(
    $_POST['copy_id'] ?? $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
);

$copy = null;

if ($copyId !== false) {
    $copy = $stockRepository->findSecondHandCopy(
        $copyId
    );
}

if ($copy === null):
?>
    <section class="panel">
        <h1>Second-hand copy not found</h1>

        <p>
            The selected copy could not be found.
        </p>

        <a class="button" href="index.php?page=stock">
            Return to stock
        </a>
    </section>
<?php
    return;
endif;

$value = function (string $field) use ($copy): string {
    return Support::escape(
        $_POST[$field] ?? $copy[$field] ?? ''
    );
};

$selectedCondition =
    $_POST['condition_grade']
    ?? $copy['condition_grade'];

$conditions = [
    'As New',
    'Very Good',
    'Good',
    'Fair',
    'Reading Copy',
];

?>
<section class="page-heading">
    <p class="eyebrow">STK-04</p>

    <h1>Update second-hand copy</h1>

    <p>
        Changes apply only to copy
        #<?= Support::escape($copy['id']) ?>.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <strong>The copy was not updated.</strong>

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
            value="update_second_hand_copy"
        >

        <input
            type="hidden"
            name="copy_id"
            value="<?= Support::escape($copy['id']) ?>"
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
                Condition *
                <select name="condition_grade" required>
                    <?php foreach (
                        $conditions as $condition
                    ): ?>
                        <option
                            value="<?= Support::escape(
                                $condition
                            ) ?>"
                            <?=
                            $selectedCondition === $condition
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
                    value="<?= $value('purchase_price') ?>"
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

            <label>
                Intake reference
                <input
                    type="text"
                    name="intake_reference"
                    value="<?= $value(
                        'intake_reference'
                    ) ?>"
                >
            </label>

            <label>
                Intake date
                <input
                    type="date"
                    name="intake_date"
                    value="<?= $value('intake_date') ?>"
                >
            </label>

            <label>
                Acquisition source
                <input
                    type="text"
                    name="acquisition_source"
                    value="<?= $value(
                        'acquisition_source'
                    ) ?>"
                >
            </label>

            <label class="full-width">
                Notes
                <textarea
                    name="notes"
                    rows="4"
                ><?= $value('notes') ?></textarea>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Update copy
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