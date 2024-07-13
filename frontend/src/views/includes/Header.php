<?php require_once(__DIR__ . '/lang.php'); ?>
<head>
    <link rel="stylesheet" href="/assets/css/header.css">
</head>

<header>
    <nav>
        <div class="logo"><a href="/HomePage">No More Waste</a></div>
        <ul>
            <li><a href="/HomePage"><?php echo htmlspecialchars($data['header_home']); ?></a></li>
            <li><a href="/Merchant/SignUp"><?php echo htmlspecialchars($data['header_merchant_signup']); ?></a></li>
            <li><a href="/Volunteer/SignUp"><?php echo htmlspecialchars($data['header_volunteer_signup']); ?></a></li>
            <li><a href="/Contact"><?php echo htmlspecialchars($data['header_contact']); ?></a></li>
            <li><a href="/Login"><?php echo htmlspecialchars($data['login_label']); ?></a></li>
        </ul>
        <div class="language-selector">
    <select onchange="changeLanguage(this.value)">
        <option value="EN" <?php echo $userLanguage == 'EN' ? 'selected' : ''; ?>>English</option>
        <option value="FR" <?php echo $userLanguage == 'FR' ? 'selected' : ''; ?>>Français</option>
        <option value="ES" <?php echo $userLanguage == 'ES' ? 'selected' : ''; ?>>Español</option>
        <option value="DE" <?php echo $userLanguage == 'DE' ? 'selected' : ''; ?>>Deutsch</option>
    </select>
    </nav>

</div>
</header>
