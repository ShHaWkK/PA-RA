<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UserController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        switch ($method) {
            case 'POST':
                return $this->createUser($input);
            case 'GET':
                if (isset($uriParts[1])) {
                    return $this->getUser((int) $uriParts[1]);
                } else {
                    return $this->getAllUsers();
                }
            case 'PUT':
                if (isset($uriParts[1])) {
                    return $this->updateUser((int) $uriParts[1], $input);
                }
                http_response_code(400);
                return ['error' => 'User ID not specified'];
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteUser((int) $uriParts[1]);
                }
                http_response_code(400);
                return ['error' => 'User ID not specified'];
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function createUser($data)
    {
        $validationResult = $this->validateUserData($data, true);
        if ($validationResult !== true) {
            http_response_code(400);
            return ['error' => $validationResult];
        }

        // Vérifie si l'email existe déjà
        $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            http_response_code(400);
            return ['error' => 'Email already exists'];
        }

        $user = new UserModel();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setRole($data['role']);
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return ['id' => $user->getId(), 'message' => 'User created successfully'];
    }

    public function getUser($id)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }
        return json_decode($this->serializer->serialize($user, 'json'), true);
    }

    public function updateUser($id, $data)
    {
        $validationResult = $this->validateUserData($data, false);
        if ($validationResult !== true) {
            http_response_code(400);
            return ['error' => $validationResult];
        }

        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            // Regarde si l'email existe déjà
            $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                http_response_code(400);
                return ['error' => 'Email already exists'];
            }
            $user->setEmail($data['email']);
        }
        // Vérifie si le mot de passe est défini et le met à jour
        if (isset($data['password'])) {
            if (strlen($data['password']) < 7) {
                http_response_code(400);
                return ['error' => 'Password must be at least 7 characters long'];
            }
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['role'])) {
            $user->setRole($data['role']);
        }
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return ['id' => $user->getId(), 'message' => 'User updated successfully'];
    }

    public function deleteUser($id)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return ['message' => 'User deleted successfully'];
    }

    public function getAllUsers()
    {
        $userRepository = $this->entityManager->getRepository(UserModel::class);
        $users = $userRepository->findAll();
        return json_decode($this->serializer->serialize($users, 'json'), true);
    }

    /*
     * Cette fonction vérifie les données utilisateur pour les champs obligatoires et les formats valides 
    */
    private function validateUserData($data, $isNew = true)
    {
        if ($isNew) {
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
                return 'Missing required fields for new user';
            }
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }

        if (isset($data['password']) && strlen($data['password']) < 7) {
            return 'Password must be at least 7 characters long';
        }

        if (isset($data['role']) && !in_array($data['role'], ['admin', 'merchant', 'volunteer', 'client'])) {
            return 'Invalid role specified';
        }

        return true;
    }
}
