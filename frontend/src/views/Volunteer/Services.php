<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Services</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/VolunteerDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Available Services</h1>

        <div class="services-table">
            <table id="servicesTable">
                <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Capacity</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <!-- Services will be populated here by JavaScript -->
                </tbody>
            </table>
        </div>

        <?php
        $loaderId = 'loadingBodyGeneral';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>

<!-- Include JavaScript -->
<script type="module" src="/assets/js/pages/VolunteerAllServicesPage.js"></script>
</body>
</html>