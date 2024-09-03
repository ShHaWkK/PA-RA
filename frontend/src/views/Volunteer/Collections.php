<!DOCTYPE html>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<html data="<?php echo strtolower($userDatauage); ?>">
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
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>
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
</div>
</body>
</html>