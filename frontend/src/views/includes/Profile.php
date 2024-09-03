<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>

<body>
<div class="column">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/HeaderBackOffice.php'); ?>
    <div class="back-office-container">
        <?php
        $userRole = $_COOKIE['user_role'] ?? '';
        $jwtToken = $_COOKIE['jwt'] ?? null;

        switch ($userRole) {
            case 'volunteer':
                requireAuth($jwtToken, 'volunteer');
                $dashboardFile = '/views/includes/VolunteerDashboard.php';
                $formFile = '/views/form/VolunteerForm.php';
                break;
            case 'admin':
                requireAuth($jwtToken, 'admin');
                $dashboardFile = '/views/includes/AdminDashboard.php';
                $formFile = '/views/form/VolunteerForm.php';
                break;
            case 'merchant':
                requireAuth($jwtToken, 'merchant');
                $dashboardFile = '/views/includes/MerchantDashboard.php';
                $formFile = '/views/form/MerchantForm.php';
                break;
        }
        require_once ($_SERVER['DOCUMENT_ROOT'] . $dashboardFile);
        ?>

        <div class="back-office-content">
            <h1> <?php echo $data['profile_label'] ?>
                <button class="modify-button"><?php echo $data['modify_button'] ?></button>
            </h1>
            <center>
                <?php require_once($_SERVER['DOCUMENT_ROOT'] .$formFile); ?>
            </center>
        </div>
    </div>
</div>
<script type="module" src="../../assets/js/pages/Profile.js"></script>
</body>
</html>
