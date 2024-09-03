<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['available_collections_title']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/MerchantDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['available_collections_title']; ?></h1>

            <button id="addNotificationButton" class="add-button"><?php echo $data['make_request_button']; ?></button>

            <label for="collectionDate"><?php echo $data['collection_date_label']; ?></label>
            <input type="date" id="collectionDate" name="collection-date"/>

            <label for="collectionDateStatus"><?php echo $data['collection_date_status_label']; ?></label>
            <select id="collectionDateStatus">
                <option value="all"><?php echo $data['all_option']; ?></option>
                <option value="upcoming"><?php echo $data['upcoming_option']; ?></option>
                <option value="completed"><?php echo $data['completed_option']; ?></option>
                <option value="assigned"><?php echo $data['assigned_option']; ?></option>
            </select>

            <button id="deleteNotificationButton" class="delete-button"><?php echo $data['remove_button']; ?></button>

            <div class="product-notification-table"></div>
            <?php
            $loaderId = 'loadingBodyNotification';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>
        </div>
    </div>
</div>
<!-- Include JavaScript -->
<script type="module" src="/assets/js/pages/MerchantCollectionsPage.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/productDetailModal.php'); ?>
</body>
</html>
