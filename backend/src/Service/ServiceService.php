<?php
// Path: backend/src/Service/ServiceService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceModel;
use Entity\ServiceScheduleModel;
use Entity\ServiceRegistrationModel;
use Entity\ServiceProposalModel;

class ServiceService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createService($data)
    {
        $service = new ServiceModel();
        $service->setName($data['name']);
        $service->setDescription($data['description']);
        $service->setStartSchedule(new \DateTime($data['start_schedule']));
        $service->setEndSchedule(new \DateTime($data['end_schedule']));
        $service->setCapacity($data['capacity']);
        $service->setCurrentRegistrations(0); // Initial registrations set to 0
        $service->setStatus($data['status']);
        $service->setLocation($data['location']);

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $service;
    }

    public function getService($id)
    {
        error_log("Attempting to fetch service with ID: $id");
        
        try {
            $service = $this->entityManager->find(ServiceModel::class, $id);
            
            if (!$service) {
                error_log("No service found with ID: $id");
                return null;
            }
            
            // Log the fetched service details
//            error_log("Service found: " . json_encode([
//                'ID' => $service->getId(),
//                'Name' => $service->getName(),
//                'Description' => $service->getDescription(),
//                'Status' => $service->getStatus(),
//                'Location' => $service->getLocation(),
//                'Capacity' => $service->getCapacity(),
//                'CurrentRegistrations' => $service->getCurrentRegistrations(),
//                'StartSchedule' => $service->getStartSchedule()->format('Y-m-d H:i:s'),
//                'EndSchedule' => $service->getEndSchedule()->format('Y-m-d H:i:s'),
//            ]));
            
            return $service;
            
        } catch (\Exception $e) {
            error_log("Exception encountered while fetching service with ID: $id. Exception message: " . $e->getMessage());
            throw $e; // Re-throw the exception after logging
        }
    }
    

    public function updateService($id, $data)
    {
        $service = $this->entityManager->find(ServiceModel::class, $id);
        if (!$service) {
            throw new \Exception('Service not found');
        }

        if (isset($data['name'])) {
            $service->setName($data['name']);
        }
        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }
        if (isset($data['start_schedule'])) {
            $service->setStartSchedule(new \DateTime($data['start_schedule']));
        }
        if (isset($data['end_schedule'])) {
            $service->setEndSchedule(new \DateTime($data['end_schedule']));
        }
        if (isset($data['capacity'])) {
            $service->setCapacity($data['capacity']);
        }
        if (isset($data['status'])) {
            $service->setStatus($data['status']);
        }
        if (isset($data['location'])) {
            $service->setLocation($data['location']);
        }

        $this->entityManager->flush();

        return $service;
    }

    public function deleteService($id)
    {
        $service = $this->entityManager->find(ServiceModel::class, $id);
        if (!$service) {
            throw new \Exception('Service not found');
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();
    }

    public function getAllServices()
    {
        return $this->entityManager->getRepository(ServiceModel::class)->findAll();
    }

    public function createServiceSchedule($data)
    {
        $service = $this->entityManager->find(ServiceModel::class, $data['service_id']);
        if (!$service) {
            throw new \Exception('Service not found');
        }
    
        $schedule = new ServiceScheduleModel();
        $schedule->setService($service);
        $schedule->setStartTime(new \DateTime($data['start_time']));
        $schedule->setEndTime(new \DateTime($data['end_time']));
        $schedule->setLocation($data['location']); 
        $schedule->setCreatedAt(new \DateTime("now"));
        $schedule->setUpdatedAt(new \DateTime("now"));
    
        $this->entityManager->persist($schedule);
        $this->entityManager->flush();
    
        return $schedule;
    }
    

    public function getServiceSchedule($id)
    {
        return $this->entityManager->find(ServiceScheduleModel::class, $id);
    }

    public function updateServiceSchedule($id, $data)
    {
        error_log("Searching for Service Schedule with ID: $id");
        $schedule = $this->entityManager->find(ServiceScheduleModel::class, $id);
        if (!$schedule) {
            error_log("Service Schedule not found with ID: $id"); 
            throw new \Exception('Schedule not found');
        }
    
        // Log the original values before update
        error_log("Original Start Time: " . $schedule->getStartTime()->format('Y-m-d H:i:s'));
        error_log("Original End Time: " . $schedule->getEndTime()->format('Y-m-d H:i:s'));
        error_log("Original Location: " . $schedule->getLocation());
    
        // Update fields
        if (isset($data['start_time'])) {
            $schedule->setStartTime(new \DateTime($data['start_time']));
        }
        if (isset($data['end_time'])) {
            $schedule->setEndTime(new \DateTime($data['end_time']));
        }
        if (isset($data['location'])) {
            $schedule->setLocation($data['location']);
        }
        $schedule->setUpdatedAt(new \DateTime("now"));
    
        // Log the new values before flush
        error_log("Updated Start Time: " . $schedule->getStartTime()->format('Y-m-d H:i:s'));
        error_log("Updated End Time: " . $schedule->getEndTime()->format('Y-m-d H:i:s'));
        error_log("Updated Location: " . $schedule->getLocation());
    
        $this->entityManager->flush();
    
        // Log after flush to confirm transaction success
        error_log("Flush completed successfully for Schedule ID: $id");
    
        return $schedule;
    }
    
    

    public function deleteServiceSchedule($id)
    {
        $schedule = $this->entityManager->find(ServiceScheduleModel::class, $id);
        if (!$schedule) {
            throw new \Exception('Schedule not found');
        }

        $this->entityManager->remove($schedule);
        $this->entityManager->flush();
    }

    public function getServiceSchedulesByServiceId($serviceId)
    {
        return $this->entityManager->getRepository(ServiceScheduleModel::class)
            ->findBy(['service' => $serviceId]);
    }

    public function createServiceFromProposal($proposalId)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $proposalId);

        if (!$proposal) {
            throw new \Exception('Proposal not found');
        }

        if ($proposal->getStatus() !== 'approved') {
            throw new \Exception('Proposal must be approved before it can be made into a service');
        }

        $service = new ServiceModel();
        $service->setName($proposal->getName());
        $service->setDescription($proposal->getDescription());
        $service->setStartSchedule(new \DateTime());
        $service->setEndSchedule(new \DateTime());
        $service->setCapacity(10);
        $service->setCurrentRegistrations(0);
        $service->setStatus('open');
        $service->setLocation('Default Location');

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $service;
    }

    public function approveProposal($id)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
        if (!$proposal) {
            throw new \Exception('Proposal not found');
        }
    
        $proposal->setStatus('approved');
        $proposal->setUpdatedAt(new \DateTime());
    
        $this->entityManager->flush();
    
        return $proposal;
    }


    public function getServiceCapacity($serviceId)
    {
        // Find the service by ID
        $service = $this->entityManager->find(ServiceModel::class, $serviceId);
        if (!$service) {
            throw new \Exception('Service not found');
        }

        // Get the total capacity from the service
        $totalCapacity = $service->getCapacity();

        // Calculate the occupied capacity based on current registrations
        $occupiedCapacity = $service->getCurrentRegistrations();

        return [
            'total_capacity' => $totalCapacity,
            'occupied_capacity' => $occupiedCapacity
        ];
    }
}

?>
