<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>

    <div class="back-office-content">
        <h1>Entreprises</h1>
        
        <div class="row">
            <button class="add-button" id="addCompanyButton"> Ajouter </button>
            <button class="delete-button" id="deleteCompanyButton"> Supprimer </button>
        </div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

        <div class="companies-table"></div>
        <script type="module" src="/assets/js/pages/AdminCompanyPage.js"></script>
    </div>

    <!--    Ajout des fenêtres modales-->
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/AddCompanyModal.php'); ?>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/DeleteCompanyModal.php'); ?>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/ModifyCompanyModal.php'); ?>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/CompanySkillModal.php'); ?>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/CompanyAvailabilitiesModal.php'); ?>

</div>
</body>
</html>