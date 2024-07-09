<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <title><?php echo htmlspecialchars($data['loading_text']); ?></title>
    <link rel="stylesheet" href="/assets/css/loader.css">
</head>
<body>
    <div id="loading-body" class="hidden">
        <div class="loading-container">
            <div class="loader"></div>
            <p><?php echo htmlspecialchars($data['loading_text']); ?></p>
        </div>
    </div>
</body>
</html>
