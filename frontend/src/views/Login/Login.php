<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php');
?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['login_title'] ?? 'Login Page'); ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body>

<div class="login-container">
    <form id="loginForm" class="login-form">
        <h2><?php echo htmlspecialchars($data['login_heading'] ?? 'Login'); ?></h2>
        <div class="form-group">
            <label for="email"><?php echo htmlspecialchars($data['email_label'] ?? 'Email:'); ?></label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password"><?php echo htmlspecialchars($data['password_label1'] ?? 'Password:'); ?></label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="submit" value="<?php echo htmlspecialchars($data['submit_button1'] ?? 'Log In'); ?>">
        </div>
    </form>
</div>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php'); ?>
<script type="module" src="/assets/js/pages/LoginPage.js"></script>

</body>
</html>