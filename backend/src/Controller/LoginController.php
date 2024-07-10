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

    public function processRequest($method, $uriParts, $input)
    {
        if ($method === 'POST' && isset($uriParts[0]) && $uriParts[0] === 'login') {
            return $this->login($input);
        }

        http_response_code(405);
        return ['error' => 'Method Not Allowed'];
    }

    public function login($data)
    {
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $email = $data['email'];
        $password = $data['password'];

        $user = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $email]);

        if (!$user) {
            http_response_code(401);
            return ['error' => 'Invalid email or password'];
        }

        if (!password_verify($password, $user->getPassword())) {
            http_response_code(401);
            return ['error' => 'Invalid password'];
        }

        // Générer un token JWT
        $payload = [
            'user_id' => $user->getId(),
            'role' => $user->getRole(),
            'exp' => time() + 3600 // Expire in 1 hour
        ];
        $token = $this->jwtService->generateToken($payload);

        return ['token' => $token, 'role' =>$user->getRole()];
    }
}
?>
