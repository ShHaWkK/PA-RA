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
            <label for="collectionDate">Collection date:</label>
            <input type="date" id="collectionDate" name="collection-date"/>
            <button id="allCollectionDates" > Toutes les dates </button>
        </div>

        <div class="row">
            <button class="add-button" id="addCollectionButton"> Ajouter </button>
            <button class="delete-button" id="deleteCollectionButton"> Supprimer </button>
            <button class="modify-button" id="modifyCollectionButton"> Modifier </button>
        </div>

        <!-- Tableau des Collectes -->
        <div class="product-notification-table">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>Company</th>
                    <th>Product</th>
                    <th>Notified Quantity</th>
                    <th>Address</th>
                    <th>Wished Collection Date</th>
                    <th>Notified At</th>
                </tr>
                </thead>
            </table>
        </div>

        <!-- Tableau de sélection du chauffeur -->
        <div class="volunteer-table"></div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/CollectedProductsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/ModifyCollectionModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/AssignProductToCollectionModal.php'); ?>

        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/AdminNewCollection.js"></script>
    </div>
</div>
</body>
</html>