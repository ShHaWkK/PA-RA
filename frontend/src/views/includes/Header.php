<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>
<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No More Waste</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <!-- Ajout de la bibliothèque Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHq6N2PRSDjH4rLWTv/f8K68jjg5mZDA5tNEgHf54RBIBp0/5WnZv7g0j7NJn/w3OqAtM2sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<header class="header">
    <nav class="navbar">
    <div class="logo"><a href="/HomePage">No More Waste</a></div>
        <ul class="nav-links">
            <li><a href="/HomePage"><i class="fa fa-home"></i> <?php echo htmlspecialchars($data['header_home']); ?></a></li>
            <li><a href="/Merchant/SignUp"><i class="fa fa-store"></i> <?php echo htmlspecialchars($data['header_merchant_signup']); ?></a></li>
            <li><a href="/Volunteer/SignUp"><i class="fa fa-hands-helping"></i> <?php echo htmlspecialchars($data['header_volunteer_signup']); ?></a></li>
            <li><a href="/Contact"><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($data['header_contact']); ?></a></li>
            <li><a href="/Login"><i class="fa fa-sign-in-alt"></i> <?php echo htmlspecialchars($data['login_label']); ?></a></li>
        </ul>
        <div class="language-selector">
            <select onchange="changeLanguage(this.value)">
                <option value="EN" <?php echo $userLanguage == 'EN' ? 'selected' : ''; ?>>English</option>
                <option value="FR" <?php echo $userLanguage == 'FR' ? 'selected' : ''; ?>>Français</option>
                <option value="ES" <?php echo $userLanguage == 'ES' ? 'selected' : ''; ?>>Español</option>
                <option value="DE" <?php echo $userLanguage == 'DE' ? 'selected' : ''; ?>>Deutsch</option>
            </select>
        </div>
    </nav>
</header>

<script>
document.querySelector('.lang-btn').addEventListener('click', function() {
    document.querySelector('.lang-menu').classList.toggle('show');
});

window.onclick = function(event) {
    if (!event.target.matches('.lang-btn')) {
        var dropdowns = document.getElementsByClassName("lang-menu");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function changeLanguage(lang) {
    window.location.href = window.location.pathname + "?lang=" + lang;
}
</script>
</body>
</html>