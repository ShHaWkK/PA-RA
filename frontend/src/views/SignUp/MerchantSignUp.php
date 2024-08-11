<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['signup_title']); ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/form/MerchantForm.php'); ?>

<!-- Partie traitement API -->
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php'); ?>
<script type="module" src="/assets/js/pages/MerchantSignUp.js"></script>

</body>
</html>
