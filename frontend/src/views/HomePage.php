<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No More Waste</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Header.php'); ?>

<body>
<section class="hero">
    <div class="hero-content">
        <h1>Bienvenue chez No More Waste</h1>
        <p>Ensemble, luttons contre le gaspillage pour un avenir durable.</p>
        <a href="#about" class="btn">En savoir plus</a>
    </div>
</section>

<section id="about" class="about">
    <div class="container">
        <h2>À propos de nous</h2>
        <p>No More Waste est une association dédiée à la lutte contre le gaspillage alimentaire et matériel. Nous croyons en un monde où les ressources sont utilisées de manière responsable et durable.</p>
    </div>
</section>

<section id="activities" class="activities">
    <div class="container">
        <h2>Nos Activités</h2>
        <div class="activity">
            <h3>Collecte et Redistribution</h3>
            <p>Nous organisons des collectes de surplus alimentaires et matériels pour les redistribuer aux personnes dans le besoin.</p>
        </div>
        <div class="activity">
            <h3> et Redistribution</h3>
            <p>Nous proposons de redistribuer les surplus alimentaires aux personnes dans le besoin.</p>
        </div>
        <div class="activity">
            <h3>Partenariats</h3>
            <p>Nous travaillons avec des entreprises et d'autres organisations pour promouvoir des pratiques durables.</p>
        </div>
    </div>
</section>

<!--<section id="contact" class="contact">-->
<!--    <div class="container">-->
<!--        <h2>Contactez-nous</h2>-->
<!--        <form action="/submit-form" method="post">-->
<!--            <input type="text" name="name" placeholder="Votre Nom" required>-->
<!--            <input type="email" name="email" placeholder="Votre Email" required>-->
<!--            <textarea name="message" placeholder="Votre Message" required></textarea>-->
<!--            <button type="submit">Envoyer</button>-->
<!--        </form>-->
<!--    </div>-->
<!--</section>-->
</body>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Footer.php'); ?>

</html>
