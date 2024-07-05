<?php
// Path: backend/src/index.php
require_once "../bootstrap.php";

use Controller\UserController;

// Obtenir l'URI de la requête
$requestUri = $_SERVER['REQUEST_URI'];

// Diviser l'URI en parties en utilisant '/' comme délimiteur
$uriParts = explode('/', trim($requestUri, '/'));

// Vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Définit le type de contenu à JSON
    header('Content-Type: application/json');
    
    // Vérifie si uri[0] est définie
    if (isset($uriParts[0])) {
        switch ($uriParts[0]) {
            case 'users':
                $userController = new UserController($entityManager);
                $data = json_decode(file_get_contents('php://input'), true);
                $response = $userController->createUser($data);
                break;
            case 'hello':
                $response = ['message' => 'Hello World'];
                break;
            case 'goodbye':
                $response = ['message' => 'Goodbye World'];
                break;
            case 'greet':
                $name = isset($uriParts[1]) ? $uriParts[1] : 'Guest';
                $response = ['message' => "Hello, $name!"];
                break;
            default:
                $response = ['error' => 'Invalid endpoint'];
                http_response_code(404);
                break;
        }
    } else {
        $response = ['error' => 'No endpoint specified'];
        http_response_code(400);
    }
    
    // Encode le tableau de réponse en JSON et l'affiche
    echo json_encode($response);
} else {
    // Si la requête n'est pas une requête POST, renvoie une erreur 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
