<?php

use Bellbird\Support;

?>

<section class="page-heading auth-heading">
    <p class="eyebrow">Administrator access</p>

    <h1>Register administrator</h1>

    <p>
        Create an authorised account for managing Bellbird
        Books stock, customers and orders.
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

<section class="panel auth-panel">
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
            value="register_administrator"
        >

        <div class="form-grid">
            <label class="full-width">
                Administrator name *
                <input
                    name="name"
                    maxlength="100"
                    autocomplete="name"
                    value="<?= Support::escape(
                        $_POST['name'] ?? ''
                    ) ?>"
                    required
                >
            </label>

            <label class="full-width">
                Email address *
                <input
                    type="email"
                    name="email"
                    autocomplete="email"
                    value="<?= Support::escape(
                        $_POST['email'] ?? ''
                    ) ?>"
                    required
                >
            </label>

            <label>
                Password *
                <input
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </label>

            <label>
                Confirm password *
                <input
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </label>
        </div>

        <p class="field-help">
            Use at least eight characters with uppercase,
            lowercase and numeric characters.
        </p>

        <div class="form-actions">
            <button type="submit">
                Register administrator
            </button>

            <a
                class="button button-secondary"
                href="index.php?page=login"
            >
                Return to login
            </a>
        </div>
    </form>
</section>