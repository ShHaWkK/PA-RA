<?php
// Path: backend/src/Controller/LoginController.php
namespace Controller;

use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Exception;
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
        } elseif ($method === 'GET' && isset($uriParts[0]) && $uriParts[0] === 'checkSession') {
            return $this->checkSession($uriParts[1]);
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

        // Vérifier le statut de l'utilisateur
        if ($user->getStatus() !== 'approved') {
            http_response_code(403);
            return ['error' => 'Account not approved'];
        }

        // Générer un token JWT
        $payload = [
            'user_id' => $user->getId(),
            'role' => $user->getRole(),
            'status' => $user->getStatus(), // Inclure le statut dans le payload si nécessaire
            'exp' => time() + 3600 // Expire in 1 hour
        ];
        $token = $this->jwtService->generateToken($payload);

        return ['token' => $token, 'role' => $user->getRole(), 'id' => $user->getId()];
    }

    public function checkSession($role)
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(400);
            return ['error' => 'Missing Authorization header'];
        }

        $authHeader = $headers['Authorization'];
        list($bearer, $token) = explode(' ', $authHeader);

        if ($bearer !== 'Bearer' || empty($token)) {
            http_response_code(400);
            return ['error' => 'Invalid Authorization header format'];
        }

        try {
            $decodedToken = $this->jwtService->verifyToken($token);

            if ($decodedToken == null) {
                http_response_code(401);
                return ['error' => 'Invalid or expired token'];
            }

            if($decodedToken->role != $role) {
                http_response_code(401);
                return ['error' => 'Unauthorized'];
            }

            return ['valid' => true, 'user_id' => $decodedToken->user_id, 'role' => $decodedToken->role];
        } catch (Exception $e) {
            http_response_code(401);
            return ['error' => 'Invalid or expired token'];
        }
    }

}
?>
