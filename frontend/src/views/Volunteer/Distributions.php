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
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>
        <div class="back-office-content">
            <h1><?php echo $data['distribution_dashboard_title']; ?></h1>

            <div class="row">
                <label for="distributionDate"><?php echo $data['distribution_date_label']; ?></label>
                <input type="date" id="distributionDate" name="distribution-date"/>
                <button id="allDistributionDates"><?php echo $data['all_dates_button']; ?></button>
            </div>

            <div class="row">
                <label for="completionSelector"><?php echo $data['completion_label']; ?></label>

                <select name="completion" id="completionSelector">
                    <option value=""><?php echo $data['all_option']; ?></option>
                    <option value="in_progress"><?php echo $data['in_progress_option']; ?></option>
                    <option value="pending"><?php echo $data['pending_option']; ?></option>
                    <option value="completed"><?php echo $data['completed_option']; ?></option>
                </select>
            </div>

            <!-- Tableau des tournées -->
            <div class="distribution-table">
                <table>
                </table>
            </div>

            <?php
            $loaderId = 'loadingBodyGeneral';
            include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
            ?>

            <!-- Script pour gérer les interactions avec la page -->
            <script type="module" src="/assets/js/pages/VolunteerAllDistributionsPage.js"></script>
        </div>
    </div>
</div>
</body>
</html>
