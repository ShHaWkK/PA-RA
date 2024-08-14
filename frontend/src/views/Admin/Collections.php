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
        <h1>Tableau de Bord des Collectes</h1>

        <!-- Tableau des Collectes -->
        <div class="collection-table">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Entreprise</th>
                    <th>Produit</th>
                    <th>Véhicule</th>
                    <th>Date de Collecte</th>
                    <th>Date de Création</th>
                    <th>Date de Mise à Jour</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="collectionTableBody">
                <!-- Les lignes du tableau seront générées dynamiquement par JavaScript -->
                </tbody>
            </table>
        </div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/VolunteerDetailsModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/VehicleDetailsModal.php'); ?>

        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/AdminCollectionPage.js"></script>
<!--        <script src="/assets/js/modules/modals/CollectionModals.js"></script>-->
<!--        <script src="/assets/js/modules/modals/VehicleManagementModals.js"></script>-->
    </div>
</div>
</body>
</html>