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
    <link rel="stylesheet" href="/assets/css/formvolunteer.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Companies</h1>

        <div class="row">
            <button class="add-button" id="addCompanyButton">Ajouter</button>
            <button class="delete-button" id="deleteCompanyButton"> Supprimer </button>
            <button class="modify-button" id="modifyCompanyButton"> Modifier </button>
        </div>

        <div class="company-table">
            <table>
            </table>
        </div>

        <?php
        $loaderId = 'loadingBodyCompany';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCompanyPage/addCompanyModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCompanyPage/editCompanyModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCompanyPage/EmployeesModal.php'); ?>


        <!-- Script pour gérer les interactions avec la page -->
        <script type="module" src="/assets/js/pages/AdminCompanyPage.js"></script>

    </div>
</div>
</body>
</html>