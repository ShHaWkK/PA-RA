<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['distribution_dashboard_title']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['distribution_dashboard_title']; ?></h1>
            <div class="row">
                <label for="distributionDate"><?php echo $data['create_route_start_date']; ?></label>
                <input type="date" id="distributionDate" name="distribution-date"/>
                <button id="allDistributionDates"><?php echo $data['dates_all']; ?></button>
            </div>

            <div class="row">
                <label for="completionSelector"><?php echo $data['status_all']; ?>:</label>
                <select name="completion" id="completionSelector">
                    <option value=""><?php echo $data['status_all']; ?></option>
                    <option value="in_progress"><?php echo $data['status_pending']; ?></option>
                    <option value="pending"><?php echo $data['status_pending']; ?></option>
                    <option value="completed"><?php echo $data['status_approved']; ?></option>
                </select>
            </div>

            <div class="row">
                <input class="add-button" type="button" onclick="location.href='/Admin/Distributions/NewDistribution';" value="<?php echo $data['distribution_new_button']; ?>" />
                <button class="delete-button" id="deleteRouteButton"><?php echo $data['distribution_delete_button']; ?></button>
                <button class="modify-button" id="modifyRouteButton"><?php echo $data['distribution_modify_button']; ?></button>
            </div>

            <div class="distribution-table">
                <table></table>
            </div>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/DestinationsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/DeliveriesModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/EditRouteModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/AssignDestinationToRoute.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/EditDestinationModal.php'); ?>

            <script type="module" src="/assets/js/pages/AdminDistributionPage.js"></script>

        </div>
    </div>
</div>
</body>
</html>