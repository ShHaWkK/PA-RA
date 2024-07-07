<?php
// Path: backend/bootstrap.php
require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
//use Dotenv\Dotenv;

// Charge les variables d'environnement depuis le fichier .env
//$dotenv = Dotenv::createImmutable(__DIR__);
//$dotenv->load();

$paths = [__DIR__ . '/src/Entity']; // Chemin vers les modèles (entités)
$isDevMode = true;

// Configuration de la connexion à la base de données
$dbParams = [
    'driver'   => getenv('MYSQL_DRIVER'),
    'host'     => getenv('MYSQL_HOST'),
    'port'     => getenv('MYSQL_PORT'),
    'user'     => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
    'dbname'   => getenv('MYSQL_DATABASE'),
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
$jwtSecret = getenv('JWT_SECRET');

if (!$jwtSecret) {
    error_log("JWT_SECRET is not set.");
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

?>
