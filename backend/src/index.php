<?php
// Path: backend/src/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../bootstrap.php";

use Controller\UserController;
use Controller\CompanyController;
use Controller\SkillController;
use Controller\AvailabilityController;
use Controller\CollectionController;
use Controller\DeliveryController;
use Service\PDFService;
use Controller\ProductController;
use Service\JWTService;
use Middleware\JWTMiddleware;
use Controller\LoginController;
use Controller\PrivateAreaController;

error_log("Traitement de la requête: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

$secretKey = getenv('JWT_SECRET'); 
$jwtService = new JWTService($secretKey);
$jwtMiddleware = new JWTMiddleware($jwtService);

// Obtenir l'URI de la requête
$requestUri = $_SERVER['REQUEST_URI'];
error_log("Requête URI: " . $requestUri);

// Diviser l'URI en parties en utilisant '/' comme délimiteur
$uriParts = explode('/', trim($requestUri, '/'));
if ($uriParts[0] === '') {
    echo 'Welcome to No More Waste API';
    exit;
}

// Instancie le service PDF
$pdfService = new PDFService();

// Mappe les contrôleurs aux chemins d'URI
$controllerMap = [
    'users' => UserController::class,
    'companies' => CompanyController::class,
    'skills' => SkillController::class,
    'availabilities' => AvailabilityController::class,
    'collections' => CollectionController::class,
    'deliveries' => DeliveryController::class,
    'products' => ProductController::class,
    'reminders' => ReminderController::class,
    'login' => LoginController::class,
    'admin' => PrivateAreaController::class,
    'volunteer' => PrivateAreaController::class,
    'merchant' => PrivateAreaController::class,
];

// Vérifie si le contrôleur existe pour le premier élément de l'URI
if (array_key_exists($uriParts[0], $controllerMap)) {
    $controllerClass = $controllerMap[$uriParts[0]];
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
    exit;
}

// Instancie le contrôleur approprié
try {
    if ($controllerClass === DeliveryController::class) {
        $controller = new $controllerClass($entityManager, $pdfService);
    } elseif ($controllerClass === LoginController::class) {
        $controller = new $controllerClass($entityManager, $jwtService);
    } else {
        $controller = new $controllerClass($entityManager);
    }
    error_log("$controllerClass instancié avec succès.");
} catch (Exception $e) {
    error_log("Erreur lors de l'instanciation de $controllerClass: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

// Obtenir les données d'entrée
$input = json_decode(file_get_contents('php://input'), true);
error_log("Données d'entrée: " . json_encode($input));

try {
    if ($uriParts[0] === 'users' && isset($uriParts[2]) && $uriParts[1] === 'approveUser') {
        // Approuver un utilisateur
        $decodedToken = $jwtMiddleware->verifyToken();
        $response = $controller->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input, $uriParts[2]);
    } elseif ($uriParts[0] === 'login') {
        // Traiter la requête de login
        $response = $controller->login($input);
    } else {
        // Routes protégées
        $decodedToken = $jwtMiddleware->verifyToken();
        if (in_array($uriParts[0], ['admin', 'volunteer', 'merchant'])) {
            $response = $controller->privateArea($uriParts[0], $decodedToken);
        } else {
            $response = $controller->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input);
        }
    }
} catch (EntityNotFoundException $e) {
    http_response_code(404);
    $response = ['error' => $e->getMessage()];
    error_log("EntityNotFoundException: " . $e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => 'Internal Server Error'];
    error_log("Exception: " . $e->getMessage());
}

// Définit le type de contenu à JSON et encode le tableau de réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
