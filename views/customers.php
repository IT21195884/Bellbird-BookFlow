<?php

use Bellbird\Support;

$customers =
    $customerOrderRepository->getCustomers();

$submittedAction = $_POST['action'] ?? '';

$customerValue = function (
    string $field,
    string $default = ''
) use ($submittedAction): string {
    if ($submittedAction !== 'create_customer') {
        return Support::escape($default);
    }

    return Support::escape(
        $_POST[$field] ?? $default
    );
};

$selectedContactMethod =
    $submittedAction === 'create_customer'
        ? ($_POST['preferred_contact'] ?? '')
        : '';

$contactMethods = [
    'Call',
    'Text',
    'Email',
    'Customer will contact shop',
];

?>

<section class="page-heading">
    <p class="eyebrow">ORD-01</p>

    <h1>Customer records</h1>

    <p>
        Record customer contact details, communication
        preferences and relevant notes.
    </p>
</section>

<?php if ($errors !== []): ?>
    <div class="message message-error">
        <strong>The customer was not saved.</strong>

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
    <h2>Add customer</h2>

    <p class="help-text">
        Enter either a phone number, an email address, or both,
        depending on the customer's preferred contact method.
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
            value="create_customer"
        >

        <div class="form-grid">
            <label>
                Customer name *

                <input
                    type="text"
                    name="name"
                    minlength="2"
                    maxlength="100"
                    autocomplete="name"
                    placeholder="For example: Taylor Morgan"
                    value="<?= $customerValue('name') ?>"
                    required
                >

                <small class="field-help">
                    Enter at least two characters.
                </small>
            </label>

            <label>
                Phone

                <input
                    type="tel"
                    name="phone"
                    maxlength="25"
                    pattern="[0-9+() .-]{8,25}"
                    autocomplete="tel"
                    placeholder="For example: 0400 123 456"
                    title="Enter 8 to 25 characters using numbers, spaces, +, brackets, dots or hyphens."
                    value="<?= $customerValue('phone') ?>"
                >

                <small class="field-help">
                    Required when Call or Text is selected.
                </small>
            </label>

            <label>
                Email

                <input
                    type="email"
                    name="email"
                    maxlength="254"
                    autocomplete="email"
                    placeholder="customer@example.com"
                    value="<?= $customerValue('email') ?>"
                >

                <small class="field-help">
                    Required when Email is selected.
                </small>
            </label>

            <label>
                Preferred contact *

                <select
                    name="preferred_contact"
                    required
                >
                    <option value="">
                        Select contact preference
                    </option>

                    <?php foreach (
                        $contactMethods as $method
                    ): ?>
                        <option
                            value="<?= Support::escape(
                                $method
                            ) ?>"
                            <?=
                            $selectedContactMethod ===
                            $method
                                ? 'selected'
                                : ''
                            ?>
                        >
                            <?= Support::escape(
                                $method
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <small class="field-help">
                    Choose how Bellbird Books should contact
                    this customer.
                </small>
            </label>

            <label class="full-width">
                Customer preferences and notes

                <textarea
                    name="notes"
                    rows="3"
                    maxlength="1000"
                    placeholder="Preferred authors, genres or other relevant notes"
                ><?= $customerValue('notes') ?></textarea>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Save customer
            </button>

            <button
                type="reset"
                class="button-secondary"
            >
                Clear form
            </button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>Recorded customers</h2>

    <p class="help-text">
        Customer information must only be accessed by
        authorised Bellbird Books staff.
    </p>

    <?php if ($customers === []): ?>
        <p class="empty-state">
            No customers have been recorded.
        </p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Contact preference</th>
                        <th>Preferences and notes</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach (
                        $customers as $customer
                    ): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= Support::escape(
                                        $customer['name']
                                    ) ?>
                                </strong>
                            </td>

                            <td>
                                <?php if (
                                    empty($customer['phone'])
                                ): ?>
                                    <span class="secondary-text">
                                        Not provided
                                    </span>
                                <?php else: ?>
                                    <a
                                        href="tel:<?= Support::escape(
                                            $customer['phone']
                                        ) ?>"
                                    >
                                        <?= Support::escape(
                                            $customer['phone']
                                        ) ?>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (
                                    empty($customer['email'])
                                ): ?>
                                    <span class="secondary-text">
                                        Not provided
                                    </span>
                                <?php else: ?>
                                    <a
                                        href="mailto:<?= Support::escape(
                                            $customer['email']
                                        ) ?>"
                                    >
                                        <?= Support::escape(
                                            $customer['email']
                                        ) ?>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="status-badge">
                                    <?= Support::escape(
                                        $customer[
                                            'preferred_contact'
                                        ]
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= Support::escape(
                                    $customer['notes']
                                    ?? '—'
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>