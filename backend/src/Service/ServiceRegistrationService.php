<?php
// Path: backend/src/Service/ServiceRegistrationService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceRegistrationModel;
use Entity\ServiceModel;
use Entity\UserModel;

class ServiceRegistrationService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createRegistration($data)
    {
        $service = $this->entityManager->find(ServiceModel::class, $data['service_id']);
        $user = $this->entityManager->find(UserModel::class, $data['user_id']);

        if (!$service) {
            throw new \Exception('Service not found');
        }

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Vérifiez si l'utilisateur est déjà inscrit
        $existingRegistration = $this->entityManager->getRepository(ServiceRegistrationModel::class)
            ->findOneBy(['service_id' => $data['service_id'], 'user_id' => $data['user_id']]);

        if ($existingRegistration) {
            throw new \Exception('User is already registered for this service');
        }

        // Vérifiez s'il reste des places disponibles
        if ($service->getCurrentRegistrations() >= $service->getCapacity()) {
            throw new \Exception('No more slots available for this service');
        }

        // Créer l'inscription
        $registration = new ServiceRegistrationModel();
        $registration->setServiceId($data['service_id']);
        $registration->setUserId($data['user_id']);
        $registration->setRegistrationDate(new \DateTime());
        $registration->setCreatedAt(new \DateTime());
        $registration->setUpdatedAt(new \DateTime());

        // Sauvegarde l'inscription
        $this->entityManager->persist($registration);

        // Met à jour le nombre d'inscriptions actuelles
        $service->setCurrentRegistrations($service->getCurrentRegistrations() + 1);

        // Sauvegarde tous les changements
        $this->entityManager->flush();

        return $registration;
    }

    public function getRegistration($id)
    {
        return $this->entityManager->find(ServiceRegistrationModel::class, $id);
    }

    public function updateRegistration($id, $data)
    {
        $registration = $this->entityManager->find(ServiceRegistrationModel::class, $id);
        if (!$registration) {
            throw new \Exception('Registration not found');
        }

        if (isset($data['service_id'])) {
            $registration->setServiceId($data['service_id']);
        }

        if (isset($data['user_id'])) {
            $registration->setUserId($data['user_id']);
        }

        $registration->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $registration;
    }

    public function deleteRegistration($id)
    {
        $registration = $this->entityManager->find(ServiceRegistrationModel::class, $id);
        if (!$registration) {
            throw new \Exception('Registration not found');
        }

        $service = $this->entityManager->find(ServiceModel::class, $registration->getServiceId());
        if ($service) {
            $service->setCurrentRegistrations($service->getCurrentRegistrations() - 1);
        }

        $this->entityManager->remove($registration);
        $this->entityManager->flush();
    }

    public function getAllRegistrations()
    {
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findAll();
    }

    public function getRegistrationsByServiceId($serviceId)
    {
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findBy(['service_id' => $serviceId]);
    }

    public function getRegistrationsByUser($userId)
    {
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findBy(['user_id' => $userId]);
    }
}
?>
