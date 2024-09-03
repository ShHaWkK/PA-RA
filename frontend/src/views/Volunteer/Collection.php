<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collecte du <span id="collect-date"></span></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/collections.css">
    <link rel="stylesheet" href="/assets/css/formvolunteer.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>

        <div class="back-office-content">

            <h1>Collecte du <span id="collect-date-header"></span></h1>

            <div id="collecte-sections">
                <!-- Sections des produits seront ajoutées ici dynamiquement -->
                <?php
                $loaderId = 'loadingBodyCollectedProducts';
                include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
                ?>
            </div>
        </div>
    </div>
</div>
<script type="module" src="/assets/js/pages/VolunteerCollectionPage.js"></script>
</body>
</html>