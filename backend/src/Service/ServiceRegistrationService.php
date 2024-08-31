<?php
// Path: backend/src/Service/ServiceRegistrationService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceRegistrationModel;
use Entity\ServiceModel;
use Entity\UserModel;
use Service\EmailService;

class ServiceRegistrationController
{
    private $entityManager;
    private $serializer;
    private $serviceRegistrationService;

    public function __construct(EntityManager $entityManager, EmailService $emailService) // Updated to include EmailService
    {
        $this->entityManager = $entityManager;
        $this->serviceRegistrationService = new ServiceRegistrationService($entityManager, $emailService); // Pass both dependencies

        // Configure normalizer for date formats
        $normalizers = [
            new DateTimeNormalizer(['datetime_format' => 'Y-m-d H:i:s']),
            new ObjectNormalizer()
        ];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
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
        
        // Vérification de la date actuelle par rapport à la date de début du service
        $currentDateTime = new \DateTime();
        if ($service->getStartSchedule() < $currentDateTime) {
            throw new \Exception('You cannot register for a service that has already started.');
        }

        // Check if the user is already registered for another service with a conflicting schedule
        $conflictingRegistrations = $this->entityManager->getRepository(ServiceRegistrationModel::class)
            ->createQueryBuilder('r')
            ->join('r.service', 's')
            ->where('r.user_id = :user_id')
            ->andWhere(':start_time BETWEEN s.start_schedule AND s.end_schedule')
            ->setParameter('user_id', $data['user_id'])
            ->setParameter('start_time', $service->getStartSchedule())
            ->getQuery()
            ->getResult();
    
        if (!empty($conflictingRegistrations)) {
            throw new \Exception('You are already registered for another service at the same time.');
        }
    
        // Check if the user is already registered for this service
        $existingRegistration = $this->entityManager->getRepository(ServiceRegistrationModel::class)
            ->findOneBy(['service' => $service, 'user_id' => $data['user_id']]);
    
        if ($existingRegistration) {
            throw new \Exception('User is already registered for this service');
        }
    
        // Check if there are slots available
        if ($service->getCurrentRegistrations() >= $service->getCapacity()) {
            throw new \Exception('No more slots available for this service');
        }
    
        // Create the registration
        $registration = new ServiceRegistrationModel();
        $registration->setService($service);
        $registration->setUserId($data['user_id']);
        $registration->setRegistrationDate(new \DateTime());
        $registration->setCreatedAt(new \DateTime());
        $registration->setUpdatedAt(new \DateTime());
    
        // Persist the registration
        $this->entityManager->persist($registration);
    
        // Update the current registrations count
        $service->setCurrentRegistrations($service->getCurrentRegistrations() + 1);
    
        // Save all changes
        $this->entityManager->flush();
    
        // Send a confirmation email after successful registration creation
        $this->emailService->sendRegistrationConfirmationEmail($user, $service);
    
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
            $service = $this->entityManager->find(ServiceModel::class, $data['service_id']);
            if ($service) {
                $registration->setService($service);
            } else {
                throw new \Exception('Service not found');
            }
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
        try {
            $registration = $this->entityManager->find(ServiceRegistrationModel::class, $id);
            if (!$registration) {
                throw new \Exception('Registration not found');
            }
    
            $service = $registration->getService();
            if ($service) {
                $service->setCurrentRegistrations($service->getCurrentRegistrations() - 1);
                $this->entityManager->persist($service); // Assurez-vous que les modifications sur le service sont persistées
            }
    
            $this->entityManager->remove($registration);
            $this->entityManager->flush();
    
            // Send notification email about unsubscription
            $user = $this->entityManager->find(UserModel::class, $registration->getUserId());
            if ($user && $service) {
                $this->emailService->sendUnsubscribeNotificationEmail($user, $service);
            }
    
            return ['message' => 'Registration deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteRegistration: " . $e->getMessage());
            throw new \Exception('Error deleting registration: ' . $e->getMessage());
        }
    }
    

    public function getAllRegistrations()
    {
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findAll();
    }

    public function getRegistrationsByServiceId($serviceId)
    {
        $service = $this->entityManager->find(ServiceModel::class, $serviceId);
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findBy(['service' => $service]);
    }

    public function getRegistrationsByUser($userId)
    {
        return $this->entityManager->getRepository(ServiceRegistrationModel::class)->findBy(['user_id' => $userId]);
    }
}
?>
