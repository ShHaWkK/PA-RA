<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Registration Details</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>

    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>

        <div class="back-office-content">
            <h1 class="service-header" id="service-name-header">Service Name</h1>
            <div id="service-info"></div>
            <div id="registration-info"></div>
            <button class="delete-button" id="unsubscribeButton">Se désinscrire</button>
            <?php
            $loaderId = 'loadingServiceDetails';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>
        </div>
    </div>
</div>

<script type="module" src="../../assets/js/pages/VolunteerServicePage.js"></script>
</body>
</html>