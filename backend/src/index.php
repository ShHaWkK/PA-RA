<?php
// Path: backend/src/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//-------------------- CORS --------------------//
// Autorise les requêtes depuis localhost
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Log pour vérifier que la requête OPTIONS est reçue
    error_log("CORS preflight request received.");
    http_response_code(200);
    exit();
}

require_once "../bootstrap.php";

use Controller\ReminderController;
use Controller\UserController;
use Controller\CompanyController;
use Controller\SkillController;
use Controller\AvailabilityController;
use Controller\StockController;
use Controller\CollectionController;
use Controller\DeliveryController;
use Controller\ServiceController;
use Controller\ProductController;
use Controller\LoginController;
use Controller\PrivateAreaController;
use Controller\ServiceProposalController;
use Controller\TicketController;
use Service\PDFService;
use Service\JWTService;
use Service\EmailService;
use Middleware\JWTMiddleware;

error_log("Traitement de la requête: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

$secretKey = getenv('JWT_SECRET');
$jwtService = new JWTService($secretKey);
$jwtMiddleware = new JWTMiddleware($jwtService);

// Obtenir l'URI de la requête
$requestUri = $_SERVER['REQUEST_URI'];
error_log("Requête URI: " . $requestUri);

// On ignore les paramètres de l'URI
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Diviser l'URI en parties en utilisant '/' comme délimiteur
$uriParts = explode('/', trim($requestUri, '/'));
if ($uriParts[0] === '') {
    echo 'Welcome to No More Waste API';
    exit;
}

// Google Maps API Key
$googleMapsApiKey = 'AIzaSyA0nZoj1xey1WSaaA_BdLH5CRca48aYQC0';

// Instancie le service PDF
$pdfService = new PDFService($googleMapsApiKey);

// Mappe les contrôleurs aux chemins d'URI
$controllerMap = [
    'users' => UserController::class,
    'companies' => CompanyController::class,
    'skills' => SkillController::class,
    'stocks' => StockController::class,
    'availabilities' => AvailabilityController::class,
    'collections' => CollectionController::class,
    'deliveries' => DeliveryController::class,
    'products' => ProductController::class,
    'reminders' => ReminderController::class,
    'login' => LoginController::class,
    'checkSession' => LoginController::class,
    'admin' => PrivateAreaController::class,
    'volunteer' => PrivateAreaController::class,
    'merchant' => PrivateAreaController::class,
    'services' => ServiceController::class,
    'service_proposals' => ServiceProposalController::class,
    'tickets' => TicketController::class,
];
// Vérifie si le contrôleur existe pour le premier élément de l'URI
$route = $uriParts[0];

// A retirer par la suite, permet de générer le token à mettre dans la table admin
if($route == 'generate_token'){
    echo json_encode(['token' => password_hash($uriParts[1], PASSWORD_BCRYPT)]);
    password_hash($uriParts[1], PASSWORD_BCRYPT);
}

if (!array_key_exists($route, $controllerMap)) {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
    exit;
}

// Instancie le contrôleur approprié
$controllerClass = $controllerMap[$route];
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
    // Vérifier les routes nécessitant une vérification JWT
    $requiresAuth = in_array($route, ['admin', 'volunteer', 'merchant']);
    if ($requiresAuth) {
        $decodedToken = $jwtMiddleware->verifyToken();
        $response = $controller->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input, $decodedToken);
    } else {
        $response = $controller->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input);
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

// Fonction pour afficher un message et quitter
function exit_with_message($message, $code = 200) {
    http_response_code($code);
    echo json_encode(['message' => $message]);
    exit;
}
?>