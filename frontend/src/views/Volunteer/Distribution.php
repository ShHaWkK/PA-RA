<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Details</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
    <link rel="stylesheet" href="/assets/css/distribution.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>

    <div class="back-office-content">
        <h1 class="route-header" id="route-name-header">Route Details</h1>
        <div id="route-info"></div>
        <div id="destinations-container"></div>
        <?php
        $loaderId = 'loadingBodyCollectedProducts';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>

<script type="module" src="../../assets/js/pages/VolunteerDistributionPage.js">
</script>
</body>
</html>