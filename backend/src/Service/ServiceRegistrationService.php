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

        // Vérifie s'il reste des places disponibles pour ce service
        $registrationsCount = $this->entityManager->getRepository(ServiceRegistrationModel::class)
            ->count(['service_id' => $service->getId()]);

        if ($registrationsCount >= $service->getCapacity()) {
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

        // Met à jour la capacité du service
        $service->setCapacity($service->getCapacity() - 1);

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

        // Augmente la capacité du service lors de la suppression d'une inscription
        $service = $this->entityManager->find(ServiceModel::class, $registration->getServiceId());
        if ($service) {
            $service->setCapacity($service->getCapacity() + 1);
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
}
?>
