<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php');
?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['volunteers_title']); ?></title>
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
            <h1><?php echo htmlspecialchars($data['volunteers_heading']); ?></h1>

            <div class="row">
                <input type="text" id="searchInput" placeholder="<?php echo htmlspecialchars($data['search_placeholder']); ?>">
                <select name="status" id="volunteer-status-select">
                    <option value=""><?php echo htmlspecialchars($data['status_all']); ?></option>
                    <option value="approved"><?php echo htmlspecialchars($data['status_approved']); ?></option>
                    <option value="rejected"><?php echo htmlspecialchars($data['status_rejected']); ?></option>
                    <option value="pending"><?php echo htmlspecialchars($data['status_pending']); ?></option>
                </select>
            </div>
            <div class="row">
                <button class="add-button" id="addVolunteerButton"><?php echo htmlspecialchars($data['add_button']); ?></button>
                <button class="delete-button" id="deleteVolunteerButton"><?php echo htmlspecialchars($data['delete_button']); ?></button>
            </div>

            <div class="row">
                <button class="add-button" id='approveBtn'><?php echo htmlspecialchars($data['approve_button']); ?></button>
                <button class="delete-button" id='rejectButton'><?php echo htmlspecialchars($data['reject_button']); ?></button>
                <button class="neutral-button" id='putOnHoldButton'><?php echo htmlspecialchars($data['put_on_hold_button']); ?></button>
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