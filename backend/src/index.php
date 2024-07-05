<?php
// Path: backend/src/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../bootstrap.php";

use Controller\UserController;

error_log("Traitement de la requête: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

// Obtenir l'URI de la requête
$requestUri = $_SERVER['REQUEST_URI'];
error_log("Requête URI: " . $requestUri);

// Diviser l'URI en parties en utilisant '/' comme délimiteur
$uriParts = explode('/', trim($requestUri, '/'));
if ($uriParts[0] === '') {
    echo 'Welcome to No More Waste API';
    exit;
}

// Appelle le contrôleur approprié en fonction de la première partie de l'URI
try {
    $userController = new UserController($entityManager);
    error_log("UserController instancié avec succès.");
} catch (Exception $e) {
    error_log("Erreur lors de l'instanciation de UserController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

// Obtenir les données d'entrée
$input = json_decode(file_get_contents('php://input'), true);
error_log("Données d'entrée: " . json_encode($input));

// Processus de la requête
try {
    $response = $userController->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input);
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
