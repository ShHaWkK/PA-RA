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
                throw new EntityNotFoundException('User ID not specified');
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteUser((int) $uriParts[1]);
                }
                throw new EntityNotFoundException('User ID not specified');
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function createUser($data)
    {
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
            throw new EntityNotFoundException('User not found');
        }
        return json_decode($this->serializer->serialize($user, 'json'), true);
    }

    public function updateUser($id, $data)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            throw new EntityNotFoundException('User not found');
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
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
            throw new EntityNotFoundException('User not found');
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
}
