<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/calendar.css">
</head>

<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/MerchantDashboard.php'); ?>
    <div class="back-office-content">
        <h1>Espace commerçant</h1>
        <div id="calendar"></div>
        <!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>-->
        <script src="../../assets/js/modules/Calendar.js"></script>
    </div>
</div>
</body>