<?php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\UserModel;

class UserService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addUser($data, $role)
    {
        $user = new UserModel();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone_number']);

        // On déclare le hash dans une variable préalablement pour éviter les erreurs d'encodage dans la BDD
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->setPassword($password);

        $user->setRole($role);
        $user->setStatus('pending');
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // A supprimer:
        error_log("user->getPassword()");
        error_log($user->getPassword());
        error_log("hasshed password");
        error_log($password);

        return $user;
    }

    public function updateUserStatus($id, $status)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->setStatus($status);
        $user->setUpdatedAt(new \DateTime("now"));
        $this->entityManager->flush();

        return $user;
    }
}
?>