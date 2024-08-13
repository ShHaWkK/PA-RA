<?php
// Path: backend/src/Controller/VehicleController.php
namespace Controller;

use Entity\VehicleModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class VehicleController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createVehicle($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getVehicle((int) $uriParts[1]);
                    } else {
                        return $this->getAllVehicles();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateVehicle((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Vehicle ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteVehicle((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Vehicle ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createVehicle($data)
    {
        try {
            if (!isset($data['brand']) || !isset($data['model']) || !isset($data['license_plate']) || !isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new vehicle'];
            }

            $vehicle = new VehicleModel();
            $vehicle->setBrand($data['brand']);
            $vehicle->setModel($data['model']);
            $vehicle->setLicensePlate($data['license_plate']);
            $vehicle->setStatus($data['status']);
            if (isset($data['current_location'])) {
                $vehicle->setCurrentLocation($data['current_location']);
            }
            $vehicle->setCreatedAt(new \DateTime("now"));
            $vehicle->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();

            return ['id' => $vehicle->getId(), 'message' => 'Vehicle created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createVehicle: " . $e->getMessage());
            throw $e;
        }
    }

    public function getVehicle($id)
    {
        try {
            $vehicle = $this->entityManager->find(VehicleModel::class, $id);
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }
            return json_decode($this->serializer->serialize($vehicle, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getVehicle: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateVehicle($id, $data)
    {
        try {
            $vehicle = $this->entityManager->find(VehicleModel::class, $id);
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }

            if (isset($data['brand'])) {
                $vehicle->setBrand($data['brand']);
            }
            if (isset($data['model'])) {
                $vehicle->setModel($data['model']);
            }
            if (isset($data['license_plate'])) {
                $vehicle->setLicensePlate($data['license_plate']);
            }
            if (isset($data['status'])) {
                $vehicle->setStatus($data['status']);
            }
            if (isset($data['current_location'])) {
                $vehicle->setCurrentLocation($data['current_location']);
            }
            $vehicle->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $vehicle->getId(), 'message' => 'Vehicle updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateVehicle: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteVehicle($id)
    {
        try {
            $vehicle = $this->entityManager->find(VehicleModel::class, $id);
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }

            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();

            return ['message' => 'Vehicle deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteVehicle: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllVehicles()
    {
        try {
            $vehicleRepository = $this->entityManager->getRepository(VehicleModel::class);
            $vehicles = $vehicleRepository->findAll();
            return json_decode($this->serializer->serialize($vehicles, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllVehicles: " . $e->getMessage());
            throw $e;
        }
    }
}
?>