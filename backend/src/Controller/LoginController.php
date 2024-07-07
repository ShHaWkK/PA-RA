<?php
// Path: backend/src/Controller/LoginController.php
namespace Controller;

use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Service\JWTService;

class LoginController
{
    private $entityManager;
    private $jwtService;

    public function __construct(EntityManager $entityManager, JWTService $jwtService)
    {
        $this->entityManager = $entityManager;
        $this->jwtService = $jwtService;
    }

    public function login($data)
    {
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $email = $data['email'];
        $password = $data['password'];

        error_log("Email: $email");
        error_log("Password (plain text): $password");

        $user = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $email]);

        if (!$user) {
            error_log("User not found");
            http_response_code(401);
            return ['error' => 'Invalid email or password'];
        }

        error_log("Stored hash: " . $user->getPassword());

        if (!password_verify($password, $user->getPassword())) {
            error_log("Password verification failed");
            http_response_code(401);
            return ['error' => 'Invalid email or password'];
        }

        error_log("User authenticated successfully");

        // Générer un token JWT
        $payload = [
            'user_id' => $user->getId(),
            'role' => $user->getRole(),
            'exp' => time() + 3600 // Expire in 1 hour
        ];
        $token = $this->jwtService->generateToken($payload);

        return ['token' => $token, 'role' => $user->getRole()];
    }
}
?>
