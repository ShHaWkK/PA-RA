<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Login</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body>
    <div class="login-container">
        <form action="process_login.php" method="POST">
            <h2>Connexion</h2>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Se connecter">
            </div>
        </form>
    </div>
</body>
</html>