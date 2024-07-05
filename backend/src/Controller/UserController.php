<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Entity\UserModel;
use Doctrine\ORM\EntityManager;

class UserController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
