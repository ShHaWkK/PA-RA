<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord des Collectes</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Tableau de Bord des Distributions</h1>


        <h2>Routes :</h2>
        <div class="row">
            <label for="distributionDate">Distribution date:</label>
            <input type="date" id="distributionDate" name="distribution-date"/>
            <button id="allDistributionDates" > Toutes les dates </button>
        </div>

        <div class="row">
            <label for="completionSelector">Completion:</label>

            <select name="completion" id="completionSelector">
                <option value="">All</option>
                <option value="in_progress">In progress</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="row">
            <input class="add-button" type="button" onclick="location.href='/Admin/Distributions/NewDistribution';" value="New distribution" />
            <button class="delete-button" id="deleteRouteButton"> Supprimer </button>
            <button class="modify-button" id="modifyRouteButton"> Modifier </button>
        </div>

        <!-- Tableau des trournées -->
        <div class="distribution-table">
            <table>
            </table>
        </div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/DestinationsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/DeliveriesModal.php'); ?>

        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/AdminDistributionPage.js"></script>

    </div>
</div>
</body>
</html>