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
    <link rel="stylesheet" href="/assets/css/progressbar.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Nouvelle collecte</h1>

        <div class="row">
            <label for="collectionDate">Date souhaitée de récupération:</label>
            <input type="date" id="collectionDate" name="collection-date"/>
            <button id="allCollectionDates" > Toutes les dates </button>
        </div>

        <h2> Sélectionner les produits de la collecte</h2>
        <!-- Tableau des demandes de collecte -->
        <div class="product-notification-table"></div>
        <?php
        $loaderId = 'loadingBodyNotification';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <h2> Sélectionner l'entrepot</h2>
<!--        <select name="warehouse" id="warehouseSelect"></select>-->
        <div class="warehouse-table">
        </div>
        <?php
        $loaderId = 'loadingBodyWarehouse';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <h2> Sélectionner le véhicule</h2>
<!--        <select name="vehicle" id="vehicleSelect"></select>-->
        <div class="vehicle-table">
        </div>
        <?php
        $loaderId = 'loadingBodyVehicle';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <h2> Sélectionner le chauffeur</h2>
        <!-- Tableau de sélection du chauffeur -->
        <div class="volunteer-table">
        </div>
        <?php
        $loaderId = 'loadingBodyDriver';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <button id="createCollection" class="add-button">Envoyer</button>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/CollectedProductsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/ModifyCollectionModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/AssignProductToCollectionModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/MerchantCompaniesModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/productDetailModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerSkillModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerAvailabilitiesModal.php'); ?>

        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/AdminNewCollection.js"></script>
    </div>
    <?php
    $loaderId = 'loadingBodyGeneral';
    include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
    ?>
</div>
</body>
</html>