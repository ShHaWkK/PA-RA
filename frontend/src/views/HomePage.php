<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php');
?>

<!DOCTYPE html>
<html lang="<?php echo strtolower($userLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No More Waste</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Header.php'); ?>
<body>
<!-- Language Selection Menu -->
<div class="language-selector">
    <select onchange="changeLanguage(this.value)">
        <option value="EN" <?php echo $userLanguage == 'EN' ? 'selected' : ''; ?>>English</option>
        <option value="FR" <?php echo $userLanguage == 'FR' ? 'selected' : ''; ?>>Français</option>
        <option value="ES" <?php echo $userLanguage == 'ES' ? 'selected' : ''; ?>>Español</option>
        <option value="DE" <?php echo $userLanguage == 'DE' ? 'selected' : ''; ?>>Deutsch</option>
    </select>
</div>

<section class="hero">
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($data['welcome']); ?></h1>
        <p><?php echo htmlspecialchars($data['welcome_subtitle']); ?></p>
        <a href="#about" class="btn"><?php echo htmlspecialchars($data['learn_more']); ?></a>
    </div>
</section>

<section id="about" class="about">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['about_us']); ?></h2>
        <p><?php echo htmlspecialchars($data['about_us_text']); ?></p>
    </div>
</section>

<section id="activities" class="activities">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['our_activities']); ?></h2>
        <div class="activity">
            <h3><?php echo htmlspecialchars($data['activity_1']); ?></h3>
            <p><?php echo htmlspecialchars($data['activity_1_text']); ?></p>
        </div>
        <div class="activity">
            <h3><?php echo htmlspecialchars($data['activity_2']); ?></h3>
            <p><?php echo htmlspecialchars($data['activity_2_text']); ?></p>
        </div>
        <div class="activity">
            <h3><?php echo htmlspecialchars($data['activity_3']); ?></h3>
            <p><?php echo htmlspecialchars($data['activity_3_text']); ?></p>
        </div>
    </div>
</section>

<!-- <section id="contact" class="contact">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['contact_us']); ?></h2>
        <form action="/submit-form" method="post">
            <input type="text" name="name" placeholder="<?php echo htmlspecialchars($data['your_name']); ?>" required>
            <input type="email" name="email" placeholder="<?php echo htmlspecialchars($data['your_email']); ?>" required>
            <textarea name="message" placeholder="<?php echo htmlspecialchars($data['your_message']); ?>" required></textarea>
            <button type="submit"><?php echo htmlspecialchars($data['send']); ?></button>
        </form>
    </div>
</section> -->
</body>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Footer.php'); ?>

</html>

<script>
function changeLanguage(lang) {
    window.location.href = window.location.pathname + "?lang=" + lang;
}
</script>
