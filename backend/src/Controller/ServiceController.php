<?php
// Path: backend/src/Controller/ServiceController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Entity\ServiceModel;
use Service\ServiceService;

class ServiceController
{
    private $entityManager;
    private $serializer;
    private $serviceService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->serviceService = new ServiceService($entityManager);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createService($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getService((int) $uriParts[1]);
                    } else {
                        return $this->getAllServices();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateService((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Service ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteService((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Service ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createService($data)
    {
        try {
            if (!isset($data['name']) || !isset($data['description']) || !isset($data['schedule']) || !isset($data['capacity']) || !isset($data['status']) || !isset($data['location'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new service'];
            }

            $service = $this->serviceService->createService($data);
            return ['id' => $service->getId(), 'message' => 'Service created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createService: " . $e->getMessage());
            throw $e;
        }
    }

    public function getService($id)
    {
        try {
            $service = $this->serviceService->getService($id);
            if (!$service) {
                http_response_code(404);
                return ['error' => 'Service not found'];
            }
            return json_decode($this->serializer->serialize($service, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getService: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateService($id, $data)
    {
        try {
            $service = $this->serviceService->updateService($id, $data);
            return ['id' => $service->getId(), 'message' => 'Service updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateService: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteService($id)
    {
        try {
            $this->serviceService->deleteService($id);
            return ['message' => 'Service deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteService: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllServices()
    {
        try {
            $services = $this->serviceService->getAllServices();
            return json_decode($this->serializer->serialize($services, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllServices: " . $e->getMessage());
            throw $e;
        }
    }
}
