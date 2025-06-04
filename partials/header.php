<?php
session_start();
include("db.php");
include("functions.php");

// Gauti aktyvų puslapio pavadinimą
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flip Book App</title>

    <!-- Bendra stilistika -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/contact.css">

    <!-- Tik adminui -->
    <?php if ($currentPage === 'admin.php'): ?>
        <link rel="stylesheet" href="css/admin.css">
    <?php endif; ?>

    <!-- Tik dashboard -->
    <?php if ($currentPage === 'dashboard.php'): ?>
        <link rel="stylesheet" href="css/dashboard.css">
    <?php endif; ?>
    <?php if ($currentPage === 'account.php'): ?>
        <link rel="stylesheet" href="css/account.css">
    <?php endif; ?>
</head>
<body class="<?php echo getPageClass(); ?>">
