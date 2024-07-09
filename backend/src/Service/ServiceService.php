<?php
// Path: backend/src/Service/ServiceService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceModel;

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
        $service->setSchedule(new \DateTime($data['schedule']));
        $service->setCapacity($data['capacity']);
        $service->setStatus($data['status']);
        $service->setLocation($data['location']);

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $service;
    }

    public function getService($id)
    {
        return $this->entityManager->find(ServiceModel::class, $id);
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
        if (isset($data['schedule'])) {
            $service->setSchedule(new \DateTime($data['schedule']));
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
}
