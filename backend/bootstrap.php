<?php
// Path: backend/bootstrap.php
require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;

// Charge les variables d'environnement depuis le fichier .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$paths = [__DIR__ . '/src/Entity']; // Chemin vers les modèles (entités)
$isDevMode = true;

// Configuration de la connexion à la base de données
$dbParams = [
    'driver'   => $_ENV['MYSQL_DRIVER'],
    'host'     => $_ENV['MYSQL_HOST'], 
    'port'     => $_ENV['MYSQL_PORT'],
    'user'     => $_ENV['MYSQL_USER'], 
    'password' => $_ENV['MYSQL_PASSWORD'],
    'dbname'   => $_ENV['MYSQL_DATABASE'],
];

// Configuration de Doctrine ORM
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Création de la connexion à la base de données
$connection = DriverManager::getConnection($dbParams, $config);

// Création de l'EntityManager
$entityManager = EntityManager::create($connection, $config);

// Vérification de la connexion
try {
    $entityManager->getConnection()->connect();
    // echo "Connexion à la base de données établie avec succès.";
} catch (\Exception $e) {
    // echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
}

// Récupération de la clé secrète JWT
$jwtSecret = $_ENV['JWT_SECRET'];

if (!$jwtSecret) {
    error_log("JWT_SECRET is not set.");
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

?>
