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

<form id="registrationForm">
    <h2><?php echo htmlspecialchars($data['signup_heading']); ?></h2>
    <div>
        <label for="first_name"><?php echo htmlspecialchars($data['first_name_label']); ?></label>
        <input type="text" id="first_name" name="first_name" required>
    </div>
    <div>
        <label for="last_name"><?php echo htmlspecialchars($data['last_name_label']); ?></label>
        <input type="text" id="last_name" name="last_name" required>
    </div>
    <div>
        <label for="email"><?php echo htmlspecialchars($data['email_label']); ?></label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="phone_number"><?php echo htmlspecialchars($data['phone_label']); ?></label>
        <input type="tel" id="phone_number" name="phone_number" required>
    </div>
    <div>
        <label for="password"><?php echo htmlspecialchars($data['password_label2']); ?></label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="company_name"><?php echo htmlspecialchars($data['company_name_label']); ?></label>
        <input type="text" id="company_name" name="company_name" required>
    </div>
    <div>
        <label for="siret"><?php echo htmlspecialchars($data['siret_label']); ?></label>
        <input type="text" id="siret" name="siret" required>
    </div>
    <div>
        <label for="address"><?php echo htmlspecialchars($data['address_label']); ?></label>
        <input type="text" id="address" name="address" required>
    </div>
    <div>
        <label for="renewal_date"><?php echo htmlspecialchars($data['renewal_date_label']); ?></label>
        <input type="date" id="renewal_date" name="renewal_date" required>
    </div>
    <div>
        <button type="submit"><?php echo htmlspecialchars($data['submit_button2']); ?></button>
    </div>
</form>

<!-- Partie traitement API -->
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php'); ?>
<script type="module" src="/assets/js/pages/MerchantSignUp.js"></script>

</body>
</html>
