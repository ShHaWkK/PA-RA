<?php
// Démarrer la session
session_start();

// Obtenir l'URL demandée
$request = $_SERVER['REQUEST_URI'];

// Fonction simple pour nettoyer l'URL
function cleanUrl($url) {
    return $url === '/' ? $url : rtrim($url, '/');
}

// Supprimer les paramètres de requête
$request = parse_url($request, PHP_URL_PATH);
$request = cleanUrl($request);

// Définir les routes
switch ($request) {
    case '':
    case '/':
    case '/HomePage':
        require __DIR__ . '/views/HomePage.php';
        break;
    case '/Merchant/SignUp':
        require __DIR__ . '/views/SignUp/MerchantSignUp.php';
        break;
    case '/Volunteer/SignUp':
        require __DIR__ . '/views/SignUp/VolunteerSignUp.php';
        break;
    case '/Login':
        require __DIR__ . '/views/Login/Login.php';
        break;
    case '/Admin':
        require __DIR__ . '/views/Admin/Volunteers.php';
        break;
    default:
        http_response_code(404);
//        require __DIR__ . '/views/404.php';
        break;
}
?>