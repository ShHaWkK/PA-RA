<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
</head>
<body>

<form id="registrationForm" >
    <div>
        <label for="first_name">Prénom</label>
        <input type="text" id="first_name" name="first_name" value="John" required>
    </div>
    <div>
        <label for="last_name">Nom</label>
        <input type="text" id="last_name" name="last_name" value="Smith" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="john2d8dqs.smidtdddh@example.com" required>
    </div>
    <div>
        <label for="phone_number">Numéro de téléphone</label>
        <input type="tel" id="phone_number" name="phone_number" value="1234567890" required>
    </div>
    <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" value="securepassword" required>
    </div>
    <div>
        <label for="company_name">Nom de l'entreprise</label>
        <input type="text" id="company_name" name="company_name" value="Corporation Enterprises" required>
    </div>
    <div>
        <label for="siret">SIRET</label>
        <input type="text" id="siret" name="siret" value="12345678901234" required>
    </div>
    <div>
        <label for="address">Adresse</label>
        <input type="text" id="address" name="address" value="123 Main St, Cityville, 12345" required>
    </div>
    <div>
        <label for="renewal_date">Date de renouvellement</label>
        <input type="date" id="renewal_date" name="renewal_date" value="2025-07-01" required>
    </div>
    <div>
        <button type="submit">Soumettre</button>
    </div>
</form>

<script src="../../assets/js/api/Users.js"></script>
<script src="../../assets/js/pages/MerchantSignUp.js"></script>

</body>
</html>