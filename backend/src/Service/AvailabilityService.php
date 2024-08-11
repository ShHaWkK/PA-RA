<?php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\AvailabilityModel;
use Entity\UserModel;

class AvailabilityService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addAvailability($data)
    {
        $user = $this->entityManager->find(UserModel::class, $data['user_id']);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $availability = new AvailabilityModel();
        $availability->setDayOfWeek($data['day_of_week']);
        $availability->setStartTime(new \DateTime($data['start_time']));
        $availability->setEndTime(new \DateTime($data['end_time']));
        $availability->setUser($user);
        $availability->setCreatedAt(new \DateTime());
        $availability->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($availability);
        $this->entityManager->flush();

        return ['id' => $availability->getId(), 'message' => 'Availability added successfully'];
    }
}
?>
