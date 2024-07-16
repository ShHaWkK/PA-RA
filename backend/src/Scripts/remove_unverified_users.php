<?php
// Path: backend/src/Scripts/remove_unverified_users.php
require_once __DIR__ . '/../../bootstrap.php';

use Doctrine\ORM\EntityManager;
use Entity\UserModel;

// Obtenir l'EntityManager
$entityManager = GetEntityManager();

$query = $entityManager->createQuery(
    'DELETE FROM Entity\UserModel u 
     WHERE u.is_verified = false 
     AND u.created_at < :date'
);

$query->setParameter('date', new \DateTime('-24 hours')); 
$result = $query->execute();

echo $result . " unverified users removed successfully.\n";
?>
