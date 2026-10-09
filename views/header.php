<?php

use Bellbird\Support;

$administrator = Support::administrator();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Bellbird BookFlow</title>

    <link
        rel="stylesheet"
        href="assets/css/styles.css"
    >
</head>

<body>
<header class="site-header">
    <a class="brand" href="index.php">
        Bellbird BookFlow
    </a>

    <nav>
        <?php if (
            Support::isAuthenticated()
        ): ?>
            <a href="index.php?page=dashboard">
                Dashboard
            </a>

            <a href="index.php?page=stock">
                Stock
            </a>

            <a href="index.php?page=customers">
                Customers
            </a>

            <a href="index.php?page=orders">
                Orders
            </a>

            <span class="nav-user">
                <?= Support::escape(
                    $administrator['name'] ?? ''
                ) ?>
            </span>

            <form
                method="post"
                class="logout-form"
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
                    value="logout_administrator"
                >

                <button
                    type="submit"
                    class="nav-button"
                >
                    Logout
                </button>
            </form>
        <?php else: ?>
            <a href="index.php?page=login">
                Login
            </a>

            <a href="index.php?page=register">
                Register
            </a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <?php if ($message !== null): ?>
        <div
            class="message message-<?= Support::escape(
                $message['type']
            ) ?>"
        >
            <?= Support::escape($message['text']) ?>
        </div>
    <?php endif; ?>