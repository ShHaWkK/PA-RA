<?php
require __DIR__ . '/../../vendor/autoload.php';  // Chemin mis à jour

use Service\JWTService;

$secretKey = '57717fdf81bb88a16e39d29987c2262879dc27029244fb190ca2d1115ef2409d';  // Remplacez ceci par votre clé secrète
$jwtService = new JWTService($secretKey);

// Générer un token
$payload = [
    'user_id' => 1,
    'role' => 'admin',
    'exp' => time() + 3600 // Token expires in 1 hour
];
$token = $jwtService->generateToken($payload);
echo "Generated Token: " . $token . PHP_EOL;

// Vérifier un token
$decoded = $jwtService->verifyToken($token);
if ($decoded) {
    echo "Token is valid. Payload: " . json_encode($decoded) . PHP_EOL;
} else {
    echo "Invalid token" . PHP_EOL;
}
