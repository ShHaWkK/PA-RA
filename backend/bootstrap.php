<?php
// Path: backend/bootstrap.php
require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$paths = ['/src/Entity']; // Chemin vers les modèles (entités)
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
?>
