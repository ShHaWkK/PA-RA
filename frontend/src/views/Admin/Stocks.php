<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['stocks_heading']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/progressbar.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['stocks_heading']; ?></h1>

            <label for="warehouseSelect"><h2><?php echo $data['dashboard_warehouses']; ?>:</h2></label>
            <select name="warehouse" id="warehouseSelect" class="warehouseSelect"></select>

            <div class="row">
                <button class="add-button" id="addStockButton"><?php echo $data['stocks_add_button']; ?></button>
                <button class="withdraw-button" id="withdrawStockButton"><?php echo $data['stocks_withdraw_button']; ?></button>
                <button class="delete-button" id="deleteStockButton"><?php echo $data['stocks_delete_button']; ?></button>
            </div>

            <div class="progress-label" id="progress-label"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <div class="stock-table"></div>
            <script type="module" src="/assets/js/pages/AdminStockPage.js"></script>
            <script src="/assets/js/modules/modals/StockModals.js"></script> </div>

        <!-- Ajout des fenêtres modales -->
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/ProductDetailModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/AddStockModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/WithdrawStockModal.php'); ?>

    </div>
</div>
</body>
</html>