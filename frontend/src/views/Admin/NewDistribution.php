<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Livraison</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Créer une Livraison</h1>

        <div id="createRouteForm">
            <div class="form-group">
                <label for="routeName">Nom de la Route :</label>
                <input type="text" id="routeName" name="name" required/>
            </div>

            <h2>Sélectionner le Véhicule</h2>
            <div class="vehicle-table"></div>
            <?php
            $loaderId = 'loadingBodyVehicle';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <h2>Sélectionner le Chauffeur</h2>
            <div class="volunteer-table"></div>

            <div class="form-group">
                <label for="startDate">Date de Début :</label>
                <input type="datetime-local" id="startDate" name="date" required>
            </div>

            <h2>Destinations</h2>
            <div id="destinationContainer"></div>
            <button type="button" id="addDestinationButton">Ajouter une Destination</button>

            <button id="createDelivery" class="add-button">Créer Livraison</button>
        </div>

        <?php
        $loaderId = 'loadingBodyDriver';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/EditDestinationModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/AssignDestinationToRoute.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/productDetailModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerSkillModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerAvailabilitiesModal.php'); ?>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

    </div>

</div>

<!-- Script pour gérer les interactions avec la page -->
<script type="module" src="/assets/js/pages/AdminNewDistribution.js"></script>
<script type="module" src="/assets/js/pages/AdminDistributionPage.js"></script>

</body>
</html>
