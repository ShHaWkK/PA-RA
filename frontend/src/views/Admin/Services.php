<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/progressbar.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Services</h1>

        <label for="serviceSelect"><h2>Service:</h2></label>
        <select name="service" id="serviceSelect"></select>

        <div class="row">
            <button class="add-button" id="addServiceButton">New Service</button>
            <button class="delete-button" id="deleteServiceButton">Delete</button>
        </div>

        <div class="progress-container">
            <div class="progress-label" id="progress-label"></div>
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <input type="hidden" id="available-capacity" />

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <div class="service-table"></div>
        <script type="module" src="/assets/js/pages/AdminServicePage.js"></script>
        <script type="module" src="/assets/js/modules/modals/ServiceModals.js"></script>
    </div>

    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/ServiceDetailModal.php'); ?>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AddServiceModal.php'); ?>
</div>
</body>
</html>
