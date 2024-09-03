<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Collections</title>
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
            <h1>Demandes de collectes</h1>

            <button id="addNotificationButton" class="add-button">Faire une demande</button>

            <label for="collectionDate"> Date:</label>
            <input type="date" id="collectionDate" name="collection-date"/>

            <label for="collectionDateStatus"> Statut </label>
            <select id="collectionDateStatus">
                <option value="all">Toutes</option>
                <option value="upcoming">A venir</option>
                <option value="completed">Effectués</option>
                <option value="assigned">Assignées</option>
            </select>

            <button id="deleteNotificationButton" class="delete-button">Retirer</button>

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