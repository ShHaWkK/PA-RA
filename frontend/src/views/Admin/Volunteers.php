<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
    <link rel="stylesheet" href="/assets/css/backoffice.css">
</head>
<body>
<div class="back-office-container">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/AdminDashboard.php'); ?>

    <div class="back-office-content">
        <h1>Bénévoles</h1>
        <input type="text" id="searchInput" placeholder="Rechercher...">
        <button>Ajouter un bénévole</button>
        <div class="volunteer-table"></div>
        <script type="module">
            import { populateVolunteerTable } from '/assets/js/pages/VolunteerTable.js';

            populateVolunteerTable();
        </script>
    </div>
</div>
</body>
</html>
