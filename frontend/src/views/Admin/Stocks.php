<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Stocks</h1>

        <label for="warehouseSelect">Warehouse:</label>
        <select name="warehouse" id="warehouseSelect"></select>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <div class="stock-table"></div>
        <script type="module" src="/assets/js/pages/AdminStockPage.js"></script>
    </div>
</div>
</body>
</html>