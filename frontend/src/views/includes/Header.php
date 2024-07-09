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
        </ul>
    </nav>
</header>
