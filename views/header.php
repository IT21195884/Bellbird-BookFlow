<?php

use Bellbird\Support;

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
        <a href="index.php?page=dashboard">Dashboard</a>
        <a href="index.php?page=stock">Stock</a>
        <a href="index.php?page=customers">Customers</a>
        <a href="index.php?page=orders">Orders</a>
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