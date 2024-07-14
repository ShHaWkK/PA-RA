<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php');
?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - No More Waste</title>
    <link rel="stylesheet" href="/assets/css/404.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHq6N2PRSDjH4rLWTv/f8K68jjg5mZDA5tNEgHf54RBIBp0/5WnZv7g0j7NJn/w3OqAtM2sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p><?php echo htmlspecialchars($data['error_message']); ?></p>
        <a href="/HomePage" class="btn"><i class="fa fa-home"></i> <?php echo htmlspecialchars($data['go_home']); ?></a>
    </div>
</body>
</html>
