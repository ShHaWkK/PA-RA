<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Double Authentification</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="auth-box">
        <h1>Double Authentification</h1>
        <p>Veuillez entrer le code que vous avez reçu par email :</p>
        <form action="/verify-code" method="post">
            <input type="text" name="auth-code" placeholder="Code de vérification" required>
            <button type="submit">Vérifier</button>
        </form>
    </div>
</div>
</body>
</html>
