<?php
// Path: backend/src/index.php
require_once "../bootstrap.php";

use Controller\UserController;

// Obtenir l'URI de la requête
$requestUri = $_SERVER['REQUEST_URI'];

// Diviser l'URI en parties en utilisant '/' comme délimiteur
$uriParts = explode('/', trim($requestUri, '/'));
if ($uriParts[0] === '') {
    echo 'Welcome to No More Waste API';
    exit;
}

// Appelle le contrôleur approprié en fonction de la première partie de l'URI
$userController = new UserController($entityManager);

// Obtenir les données d'entrée
$input = json_decode(file_get_contents('php://input'), true);

// Processus de la requête
try {
    $response = $userController->processRequest($_SERVER['REQUEST_METHOD'], $uriParts, $input);
} catch (EntityNotFoundException $e) {
    http_response_code(404);
    $response = ['error' => $e->getMessage()];
} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => 'Internal Server Error'];
}

// Définit le type de contenu à JSON et encode le tableau de réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
