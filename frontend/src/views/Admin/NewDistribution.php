<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['create_delivery_title']; ?></title>
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
            <h1><?php echo $data['create_delivery_title']; ?></h1>

            <div id="createRouteForm">
                <div class="form-group">
                    <label for="routeName"><?php echo $data['create_route_name']; ?></label>
                    <input type="text" id="routeName" name="name" required/>
                </div>

                <h2><?php echo $data['dashboard_companies']; ?></h2>
                <div class="vehicle-table"></div>
                <?php
                $loaderId = 'loadingBodyVehicle';
                include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
                ?>

                <h2><?php echo $data['dashboard_title']; ?></h2>
                <div class="volunteer-table"></div>

                <div class="form-group">
                    <label for="startDate"><?php echo $data['create_route_start_date']; ?></label>
                    <input type="datetime-local" id="startDate" name="date" required>
                </div>

                <h2><?php echo $data['dashboard_distributions']; ?></h2>
                <div id="destinationContainer"></div>
                <button type="button" id="addDestinationButton"><?php echo $data['create_delivery_add_destination']; ?></button>

                <button id="createDelivery" class="add-button"><?php echo $data['create_delivery_create']; ?></button>
            </div>

            <?php
            $loaderId = 'loadingBodyDriver';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/EditDestinationModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminDistributionPage/AssignDestinationToRoute.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminStockPage/productDetailModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerSkillModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminUsersPages/VolunteerAvailabilitiesModal.php'); ?>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

        </div>
    </div>
</div>
<!-- Script pour gérer les interactions avec la page -->
<script type="module" src="/assets/js/pages/AdminNewDistribution.js"></script>
<script type="module" src="/assets/js/pages/AdminDistributionPage.js"></script>

</body>
</html>