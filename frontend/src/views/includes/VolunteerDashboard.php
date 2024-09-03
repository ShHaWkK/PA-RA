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
        <li><a href="/Volunteer/Planning"><?php echo $data['volunteer_planning']; ?></a></li>
        <li><a href="/Volunteer/Collections"><?php echo $data['volunteer_collections']; ?></a></li>
        <li><a href="/Volunteer/Distributions"><?php echo $data['volunteer_distributions']; ?></a></li>
        <li><a href="/Volunteer/Services"><?php echo $data['volunteer_services']; ?></a></li>
        <li><a href="/Volunteer/Profile"><?php echo $data['dashboard_profile']; ?></a></li>
        <li><a href="#" id="deconnection-link"><?php echo $data['dashboard_logout']; ?></a></li>
    </ul>
</div>

<script type="module" src="../../assets/js/modules/Dashboard.js"></script>
</body>
</html>