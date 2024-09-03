<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['collections_dashboard_title']; ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/progressbar.css">
</head>
<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['collections_dashboard_heading']; ?></h1>

            <div class="row">
                <label for="collectionDate"><?php echo $data['collection_date_label']; ?></label>
                <input type="date" id="collectionDate" name="collection-date"/>
                <button id="allCollectionDates"><?php echo $data['all_dates_button']; ?></button>
            </div>

            <div class="row">
                <label for="completionSelector"><?php echo $data['completion_label']; ?></label>
                <select name="completion" id="completionSelector">
                    <option value=""><?php echo $data['all_option']; ?></option>
                    <option value="true"><?php echo $data['completed_option']; ?></option>
                    <option value="false"><?php echo $data['ongoing_option']; ?></option>
                </select>
            </div>

            <div class="row">
                <input class="add-button" type="button" onclick="location.href='/Admin/Collections/NewCollection';" value="<?php echo $data['new_collection_button']; ?>" />
                <button class="delete-button" id="deleteCollectionButton"><?php echo $data['delete_button']; ?></button>
                <button class="modify-button" id="modifyCollectionButton"><?php echo $data['modify_button']; ?></button>
            </div>

            <div class="collection-table">
                <table>
                    <thead>
                    <tr>
                        <th><?php echo $data['id_heading']; ?></th>
                        <th><?php echo $data['company_heading']; ?></th>
                        <th><?php echo $data['product_heading']; ?></th>
                        <th><?php echo $data['vehicle_heading']; ?></th>
                        <th><?php echo $data['collection_date_heading']; ?></th>
                        <th><?php echo $data['creation_date_heading']; ?></th>
                        <th><?php echo $data['update_date_heading']; ?></th>
                        <th><?php echo $data['actions_heading']; ?></th>
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

            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VolunteerDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/VehicleDetailsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/CollectedProductsModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/ModifyCollectionModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/AssignProductToCollectionModal.php'); ?>
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/modals/AdminCollectionPage/ModifyCollectionProductModal.php'); ?>

            <script type="module" src="/assets/js/pages/AdminCollectionPage.js"></script>
        </div>
    </div>
</div>
</body>
</html>
