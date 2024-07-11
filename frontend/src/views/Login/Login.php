<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Login</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>

<body>
<form id="loginForm" >
    <h2>Connexion</h2>
    <div class="form-group">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="admin@admin.com" required>
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" value="password1423" required>
    </div>
    <div class="form-group">
        <input type="submit" value="Se connecter">
    </div>
</form>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php'); ?>
<!--<script src="/assets/js/api/Login.js"></script>-->
<script type="module" src="/assets/js/pages/LoginPage.js"></script>

</body>
</html>