<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>
<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['dashboard_title']; ?></title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-sidebar">
    <h2><?php echo $data['dashboard_title']; ?></h2>
    <ul>
        <li><a href="/Admin/Volunteers"><?php echo $data['dashboard_volunteers']; ?></a></li>
        <li><a href="/Admin/Merchants"><?php echo $data['dashboard_merchants']; ?></a></li>
        <li><a href="/Admin/Companies"><?php echo $data['dashboard_companies']; ?></a></li>
        <li><a href="/Admin/Warehouses"><?php echo $data['dashboard_warehouses']; ?></a></li>
        <li><a href="/Admin/Stocks"><?php echo $data['dashboard_stocks']; ?></a></li>
        <li><a href="/Admin/Collections"><?php echo $data['dashboard_collections']; ?></a></li>
        <li><a href="/Admin/Distributions"><?php echo $data['dashboard_distributions']; ?></a></li>
        <!-- <li><a href="/Admin/Services"><?php echo $data['dashboard_services']; ?></a></li> -->
        <li><a href="/Admin/Profile"><?php echo $data['dashboard_profile']; ?></a></li>
        <li><a href="#" id="deconnection-link"><?php echo $data['dashboard_logout']; ?></a></li>
    </ul>
</div>

<script type="module" src="../../assets/js/modules/Dashboard.js"></script>
</body>
</html>
