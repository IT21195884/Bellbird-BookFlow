<?php

use Bellbird\Support;

?>

<section class="page-heading auth-heading">
    <p class="eyebrow">Secure access</p>

    <h1>Administrator login</h1>

    <p>
        Log in to manage Bellbird Books stock,
        customers and customer orders.
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
            value="login_administrator"
        >

        <div class="form-grid">
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

            <label class="full-width">
                Password *
                <input
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">
                Log in
            </button>

            <a
                class="button button-secondary"
                href="index.php?page=register"
            >
                Register administrator
            </a>
        </div>
    </form>
</section>