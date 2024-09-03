<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['warehouses_heading']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/formvolunteer.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>

    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['warehouses_heading']; ?></h1>

            <div class="row">
                <button class="add-button" id="addWarehouseButton"><?php echo $data['warehouses_add_button']; ?></button>
                <button class="delete-button" id="deleteWarehouseButton"><?php echo $data['warehouses_delete_button']; ?></button>
                <button class="modify-button" id="modifyWarehouseButton"><?php echo $data['warehouses_modify_button']; ?></button>
            </div>

            <div class="warehouse-table">
                <table></table>
            </div>

            <?php
            $loaderId = 'loadingBodyWarehouse';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminWarehousePage/addWarehouseModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminWarehousePage/editWarehouseModal.php'); ?>

            <script type="module" src="/assets/js/pages/AdminWarehousePage.js"></script>

        </div>
    </div>
</div>
</body>
</html>