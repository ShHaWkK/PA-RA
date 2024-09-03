<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteers</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>

    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>

        <div class="back-office-content">
            <h1>Bénévoles</h1>

            <div class="row">
                <input type="text" id="searchInput" placeholder="Rechercher...">
                <select name="status" id="volunteer-status-select">
                    <option value=""> Tous </option>
                    <option value="approved"> Approuvé </option>
                    <option value="rejected"> Rejeté </option>
                    <option value="pending"> En attente  </option>
                </select>
            </div>
            <div class="row">
                <button class="add-button" id="addVolunteerButton"> Ajouter </button>
                <button class="delete-button" id="deleteVolunteerButton"> Supprimer </button>
            </div>

            <div class="row">
                <button class="add-button" id='approveBtn'> Approuver </button>
                <button class="delete-button" id='rejectButton'> Refuser </button>
                <button class="neutral-button" id='putOnHoldButton'> Mettre en attente </button>
            </div>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <div class="volunteer-table"></div>
            <script type="module" src="/assets/js/pages/AdminUserPages.js"></script>
        </div>

        <!--    Ajout des fenêtres modales-->
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/AddVolunteerModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/DeleteVolunteerModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/ModifyVolunteerModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerSkillModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerAvailabilitiesModal.php'); ?>

    </div>
</div>
</body>
</html>