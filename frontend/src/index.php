<?php
ob_start(); // Start output buffering

require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); // Inclusion de lang.php

$jwtToken = isset($_COOKIE['jwt']) ? $_COOKIE['jwt'] : null;

// Fonction pour nettoyer l'URL
function cleanUrl($url) {
    return $url === '/' ? $url : rtrim($url, '/');
}

// Supprimer les paramètres de requête
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = cleanUrl($request);

// Fonction pour vérifier l'authentification
function requireAuth($jwtToken, $role) {
    try {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/js/modules/env.php');
        // Appeler la fonction JavaScript authenticate
        $authResponse = "<script type='module'>
                            import { authenticate } from '/assets/js/api/Login.js';
                            authenticate('{$jwtToken}', '{$role}').then(response => {
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
    case '/Login':
        require __DIR__ . '/views/Login/Login.php';
        break;
    case '/Merchant/SignUp':
        require __DIR__ . '/views/SignUp/MerchantSignUp.php';
        break;
    case '/Merchant/Merchants':
        if ($jwtToken) {
            requireAuth($jwtToken, 'merchant');
            require __DIR__ . '/views/Merchant/Merchant.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Merchant/Collections':
        if ($jwtToken) {
            requireAuth($jwtToken, 'merchant');
            require __DIR__ . '/views/Merchant/Collections.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/SignUp':
        require __DIR__ . '/views/SignUp/VolunteerSignUp.php';
        break;
    case '/Volunteer/Planning':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');
            require __DIR__ . '/views/Volunteer/Planning.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/Collection':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');

            // Récupérer les paramètres collectionId et date depuis l'URL
            $collectionId = isset($_GET['collectionId']) ? $_GET['collectionId'] : null;
            $date = isset($_GET['date']) ? $_GET['date'] : null;

            // Vous pouvez maintenant transmettre ces paramètres à la page ou gérer les erreurs si manquants
            if ($collectionId && $date) {
                require __DIR__ . '/views/Volunteer/Collection.php';
            } else {
                echo "Paramètres manquants : collectionId ou date";
                exit;
            }
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/Collections':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');
            require __DIR__ . '/views/Volunteer/Collections.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/Distribution':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');

            $routeId = isset($_GET['routeId']) ? $_GET['routeId'] : null;

            if ($routeId) {
                require __DIR__ . '/views/Volunteer/Distribution.php';
            } else {
                echo "Paramètre manquant : routeId";
                exit;
            }
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/Distributions':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');
                require __DIR__ . '/views/Volunteer/Distributions.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Volunteer/Service':
        if ($jwtToken) {
            requireAuth($jwtToken, 'volunteer');

            $routeId = isset($_GET['serviceRegistrationId']) || null;

            if ($routeId) {
                require __DIR__ . '/views/Volunteer/Service.php';
            } else {
                echo "Paramètre manquant : serviceId";
                exit;
            }
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Volunteers':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Volunteers.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Merchants':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Merchants.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Companies':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Companies.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Stocks':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Stocks.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Warehouses':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views//Admin/Warehouses.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Collections':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Collections.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Collections/NewCollection':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/DailyCollection.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Distributions':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Distributions.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Distributions/NewDistribution':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/NewDistribution.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Admin/Services':
        if ($jwtToken) {
            requireAuth($jwtToken, 'admin');
            require __DIR__ . '/views/Admin/Services.php';
        } else {
            require __DIR__ . '/views/Login/Login.php';
            exit;
        }
        break;
    case '/Merchant':
        if ($jwtToken) {
            requireAuth($jwtToken, 'merchant');
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

// Envoyer la sortie tamponnée à la fin du script
ob_end_flush();
?>