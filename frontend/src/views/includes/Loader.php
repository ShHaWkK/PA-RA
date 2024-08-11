<?php
// Générer un identifiant unique pour chaque instance du loader
$loaderId = $loaderId ?? uniqid('');
?>

<link rel="stylesheet" href="/assets/css/loader.css">

<div class="loading-body hidden" id="<?php echo htmlspecialchars($loaderId); ?>">
    <div class="loading-container">
        <div class="loader"></div>
        <p>Chargement...</p>
    </div>
</div>