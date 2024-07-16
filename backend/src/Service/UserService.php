<?php
namespace Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
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
        error_log("Adding user with email: " . $data['email']);

        $user = new UserModel();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone_number']);

        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->setPassword($password);

        $user->setRole($role);
        $user->setStatus('pending');
        $user->setVerificationCode($data['verification_code']); 
        $user->setIsVerified($data['is_verified']); 
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        error_log("User added successfully with ID: " . $user->getId());

        return $user;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function updateUserStatus($id, $status)
    {
        error_log("updateUserStatus()");
        $user = $this->entityManager->find(UserModel::class, $id);
        error_log("id:");
        error_log(print_r($id,true));
        error_log("status:");
        error_log(print_r($status,true));
        if (!$user) {
            error_log("not found");
            throw new \Exception('User not found');
        }
        error_log("1");
        $user->setStatus($status);
        error_log("2");
        $user->setUpdatedAt(new \DateTime("now"));
        error_log("3");
        $this->entityManager->flush();

        return $user;
    }
}
?>
