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
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Tableau de Bord des Collectes</h1>

        <div class="row">
            <label for="collectionDate">Collection date:</label>
            <input type="date" id="collectionDate" name="collection-date"/>
            <button id="allCollectionDates" > Toutes les dates </button>
        </div>

        <div class="row">
            <label for="completionSelector">Completion:</label>

            <select name="completion" id="completionSelector">
                <option value="">All</option>
                <option value="true">Completed</option>
                <option value="false">Ongoing</option>
            </select>
        </div>

        <!-- Tableau des Collectes -->
        <div class="collection-table">
            <table>
                <tbody id="collectionTableBody">
                </tbody>
            </table>
        </div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/VolunteerAllCollectionsPage.js"></script>
    </div>
</div>
</body>
</html>