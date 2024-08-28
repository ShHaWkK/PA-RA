<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['signup_title']); ?></title>
    <link rel="stylesheet" href="/assets/css/formvolunteer.css">
</head>
<body>


<form id="merchantForm">
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

    <!-- Company Selector -->
    <div id="company_selector_section">
        <label for="company_selector">Sélectionnez votre entreprise</label>
        <select id="company_selector" name="company_id" required>
            <option value="">Sélectionnez une entreprise</option>
        </select>
    </div>

    <!-- Checkbox to show company creation fields -->
    <div>
        <label for="new_company_checkbox">Votre entreprise ne figure pas dans la liste ?</label>
        <input type="checkbox" id="new_company_checkbox" name="new_company_checkbox">
    </div>

    <!-- Company creation fields (initially hidden) -->
    <div id="new_company_fields" style="display: none;">
        <div>
            <label for="company_name"><?php echo htmlspecialchars($data['company_name_label']); ?></label>
            <input type="text" id="company_name" name="company_name">
        </div>
        <div>
            <label for="siret"><?php echo htmlspecialchars($data['siret_label']); ?></label>
            <input type="text" id="siret" name="siret" maxlength="14">
        </div>
        <div>
            <label for="address"><?php echo htmlspecialchars($data['address_label']); ?></label>
            <input type="text" id="address" name="address">
        </div>
        <div>
            <label for="renewal_date"><?php echo htmlspecialchars($data['renewal_date_label']); ?></label>
            <input type="date" id="renewal_date" name="renewal_date">
        </div>
    </div>

    <div>
        <button type="submit"><?php echo htmlspecialchars($data['submit_button2']); ?></button>
    </div>
</form>