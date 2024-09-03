<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<!DOCTYPE html>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['merchant_heading']; ?></title>
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
            <h1><?php echo $data['merchant_heading']; ?></h1>

            <div class="row">
                <input type="text" id="searchInput" placeholder="<?php echo $data['search_placeholder']; ?>">
                <select name="status" id="merchant-status-select">
                    <option value=""><?php echo $data['status_all']; ?></option>
                    <option value="approved"><?php echo $data['status_approved']; ?></option>
                    <option value="rejected"><?php echo $data['status_rejected']; ?></option>
                    <option value="pending"><?php echo $data['status_pending']; ?></option>
                </select>
            </div>
            <div class="row">
                <button class="add-button" id="addMerchantButton"><?php echo $data['add_button']; ?></button>
                <button class="delete-button" id="deleteVolunteerButton"><?php echo $data['delete_button']; ?></button>
            </div>

            <div class="row">
                <button class="add-button" id='approveBtn'><?php echo $data['approve_button']; ?></button>
                <button class="delete-button" id='rejectButton'><?php echo $data['reject_button']; ?></button>
                <button class="neutral-button" id='putOnHoldButton'><?php echo $data['put_on_hold_button']; ?></button>
            </div>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <div class="merchant-table"></div>
            <script type="module" src="/assets/js/pages/AdminUserPages.js"></script>
        </div>

        <!--    Ajout des fenêtres modales-->
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/AddMerchantModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/DeleteVolunteerModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/ModifyVolunteerModal.php'); ?>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/MerchantCompaniesModal.php'); ?>

    </div>
</div>
</body>
</html>