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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHq6N2PRSDjH4rLWTv/f8K68jjg5mZDA5tNEgHf54RBIBp0/5WnZv7g0j7NJn/w3OqAtM2sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Header.php'); ?>
<body>

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

<section id="testimonials" class="testimonials">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['testimonials']); ?></h2>
        <div class="testimonial">
            <p><?php echo htmlspecialchars($data['testimonial_1']); ?></p>
            <p><strong><?php echo htmlspecialchars($data['testimonial_1_author']); ?></strong></p>
        </div>
        <div class="testimonial">
            <p><?php echo htmlspecialchars($data['testimonial_2']); ?></p>
            <p><strong><?php echo htmlspecialchars($data['testimonial_2_author']); ?></strong></p>
        </div>
    </div>
</section>

<section id="gallery" class="gallery">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['gallery']); ?></h2>
        <div class="gallery-item"><img src="/assets/images/images.jpg" alt="Gallery Image 1"></div>
        <div class="gallery-item"><img src="/assets/images/gallery2.jpg" alt="Gallery Image 2"></div>
        <div class="gallery-item"><img src="/assets/images/gallery3.jpg" alt="Gallery Image 3"></div>
    </div>
</section>

<section id="statistics" class="statistics">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['statistics']); ?></h2>
        <div class="stat">
            <i class="fa fa-users"></i>
            <p><?php echo htmlspecialchars($data['stat_volunteers']); ?></p>
        </div>
        <div class="stat">
            <i class="fa fa-hand-holding-heart"></i>
            <p><?php echo htmlspecialchars($data['stat_donations']); ?></p>
        </div>
        <div class="stat">
            <i class="fa fa-truck"></i>
            <p><?php echo htmlspecialchars($data['stat_collections']); ?></p>
        </div>
    </div>
</section>

<section id="cta" class="cta">
    <div class="container">
        <h2><?php echo htmlspecialchars($data['cta_title']); ?></h2>
        <p><?php echo htmlspecialchars($data['cta_text']); ?></p>
        <a href="/Volunteer/SignUp" class="btn"><?php echo htmlspecialchars($data['cta_button']); ?></a>
    </div>
</section>

</body>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Footer.php'); ?>

</html>

<script>
function changeLanguage(lang) {
    window.location.href = window.location.pathname + "?lang=" + lang;
}
</script>
