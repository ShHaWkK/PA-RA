<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['new_collection_title']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
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
            <h1><?php echo $data['new_collection_heading']; ?></h1>

            <div class="row">
                <label for="collectionDate"><?php echo $data['desired_collection_date_label']; ?></label>
                <input type="date" id="collectionDate" name="collection-date"/>
                <button id="allCollectionDates"><?php echo $data['all_dates_button']; ?></button>
            </div>

            <h2><?php echo $data['select_products_heading']; ?></h2>
            <div class="product-notification-table"></div>
            <?php
            $loaderId = 'loadingBodyNotification';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <h2><?php echo $data['select_warehouse_heading']; ?></h2>
            <div class="warehouse-table"></div>
            <?php
            $loaderId = 'loadingBodyWarehouse';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <h2><?php echo $data['select_vehicle_heading']; ?></h2>
            <div class="vehicle-table"></div>
            <?php
            $loaderId = 'loadingBodyVehicle';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <h2><?php echo $data['select_driver_heading']; ?></h2>
            <div class="volunteer-table"></div>
            <?php
            $loaderId = 'loadingBodyDriver';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <button id="createCollection" class="add-button"><?php echo $data['send_button']; ?></button>

            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/CollectedProductsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/ModifyCollectionModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/AssignProductToCollectionModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/MerchantCompaniesModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/productDetailModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerSkillModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerAvailabilitiesModal.php'); ?>

            <script type="module" src="/assets/js/pages/AdminNewCollection.js"></script>
        </div>
        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>
</body>
</html>
