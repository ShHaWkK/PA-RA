<?php
// Path: backend/src/Controller/PrivateAreaController.php
namespace Controller;

use Doctrine\ORM\EntityManager;

class PrivateAreaController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processRequest($method, $uriParts, $input, $decodedToken)
    {
        if ($method === 'GET' && isset($uriParts[0])) {
            return $this->privateArea($uriParts[0], $decodedToken);
        }

        http_response_code(405);
        return ['error' => 'Method Not Allowed'];
    }

    private function privateArea($role, $decodedToken)
    {
        switch ($role) {
            case 'admin':
                return $this->adminArea($decodedToken);
            case 'volunteer':
                return $this->volunteerArea($decodedToken);
            case 'merchant':
                return $this->merchantArea($decodedToken);
            default:
                http_response_code(403);
                return ['error' => 'Access denied'];
        }
    }

    private function adminArea($decodedToken)
    {
        if ($decodedToken->role !== 'admin') {
            http_response_code(403);
            return ['error' => 'Access denied'];
        }
        return ['message' => 'Welcome to the admin area'];
    }

    private function volunteerArea($decodedToken)
    {
        if ($decodedToken->role !== 'volunteer') {
            http_response_code(403);
            return ['error' => 'Access denied'];
        }
        return ['message' => 'Welcome to the volunteer area'];
    }

    private function merchantArea($decodedToken)
    {
        if ($decodedToken->role !== 'merchant') {
            http_response_code(403);
            return ['error' => 'Access denied'];
        }
        return ['message' => 'Welcome to the merchant area'];
    }
}
?>
