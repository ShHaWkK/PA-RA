<?php
$jwtToken = isset($_COOKIE['jwt']) ? $_COOKIE['jwt'] : null;

// Fonction pour nettoyer l'URL
function cleanUrl($url) {
    return $url === '/' ? $url : rtrim($url, '/');
}

// Supprimer les paramètres de requête
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = cleanUrl($request);

// Fonction pour vérifier l'authentification
function requireAuth($jwtToken,$role) {
    try {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php');
        // Appeler la fonction JavaScript authenticate
        $authResponse = "<script type='module'>
                            import { authenticate } from '/assets/js/api/Login.js';
                            authenticate('{$jwtToken}','{$role}').then(response => {
                                if (!response.valid) {
                                      alert(response.json());
                                    window.location.href = '/Login'; 
                                }
                            }).catch(error => {
                                console.error('Erreur : ', error.message);
                                alert(error.message);
                                window.location.href = '/Login'; 
                            });
                        </script>";
        echo $authResponse;
    } catch (Exception $e) {
        echo "<script type='text/javascript'>console.error('PHP error : {$e->getMessage()}');</script>";
        http_response_code(500);
        exit;
    }
}

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
        // Exemple de route protégée
        if ($jwtToken) {
            requireAuth($jwtToken,'admin');
            require __DIR__ . '/views/Admin/Volunteers.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer':
        // Exemple de route protégée
        if ($jwtToken) {
            requireAuth($jwtToken,'volunteer');
            require __DIR__ . '/views/Volunteer/Volunteer.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Merchant':
        // Exemple de route protégée
        if ($jwtToken) {
            requireAuth($jwtToken,'merchant');
            require __DIR__ . '/views/Merchant/Merchant.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    default:
        http_response_code(404);
         require __DIR__ . '/views/includes/404.php';
        break;
}
?>