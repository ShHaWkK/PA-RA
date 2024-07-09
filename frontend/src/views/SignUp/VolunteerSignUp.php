<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="../../src/assets/css/global.css">
</head>
<body>



<form id="registrationForm">
    <h2>Formulaire d'inscription</h2>
    <div>
        <label for="first_name">Prénom</label>
        <input type="text" id="first_name" name="first_name" value="John" required>
    </div>
    <div>
        <label for="last_name">Nom</label>
        <input type="text" id="last_name" name="last_name" value="Smith" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="john.dmdaoesse@example.com" required>
    </div>
    <div>
        <label for="phone_number">Numéro de téléphone</label>
        <input type="tel" id="phone_number" name="phone_number" value="1234567890" required>
    </div>
    <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" value="password1423" required>
    </div>
    <div>
        <label for="skills">Compétences (sélectionnez plusieurs en maintenant la touche Ctrl/cmd)</label>
        <select id="skills" name="skills" multiple >
        </select>
    </div>
    <div>
        <label>Disponibilités</label>
        <table id="availabilities">
            <tr>
                <th>Sélectionner</th>
                <th>Jour de la semaine</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[0][selected]" value="1"></td>
                <td><input type="text" name="availabilities[0][day_of_week]" value="Monday" readonly></td>
                <td><input type="time" name="availabilities[0][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[0][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[1][selected]" value="2"></td>
                <td><input type="text" name="availabilities[1][day_of_week]" value="Tuesday" readonly></td>
                <td><input type="time" name="availabilities[1][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[1][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[2][selected]" value="3"></td>
                <td><input type="text" name="availabilities[2][day_of_week]" value="Wednesday" readonly></td>
                <td><input type="time" name="availabilities[2][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[2][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[3][selected]" value="4"></td>
                <td><input type="text" name="availabilities[3][day_of_week]" value="Thursday" readonly></td>
                <td><input type="time" name="availabilities[3][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[3][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[4][selected]" value="5"></td>
                <td><input type="text" name="availabilities[4][day_of_week]" value="Friday" readonly></td>
                <td><input type="time" name="availabilities[4][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[4][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[5][selected]" value="6"></td>
                <td><input type="text" name="availabilities[5][day_of_week]" value="Saturday" readonly></td>
                <td><input type="time" name="availabilities[5][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[5][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[6][selected]" value="7"></td>
                <td><input type="text" name="availabilities[6][day_of_week]" value="Sunday" readonly></td>
                <td><input type="time" name="availabilities[6][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[6][end_time]" value="18:00"></td>
            </tr>
        </table>
    </div>
    <div>
        <button type="submit">Soumettre</button>
    </div>
</form>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/src/assets/js/modules/env.php'); ?>
<script src="../../src/assets/js/api/Users.js"></script>
<script src="../../src/assets/js/api/Skills.js"></script>
<script src="../../src/assets/js/pages/VolunteerSignUp.js"></script>

</body>
</html>
