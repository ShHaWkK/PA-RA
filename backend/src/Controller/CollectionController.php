<?php
// Path: backend/src/Controller/CollectionController.php
namespace Controller;

use Entity\CollectionModel;
use Entity\VehicleModel;
use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class CollectionController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createCollection($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getCollection((int) $uriParts[1]);
                    } else {
                        return $this->getAllCollections();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateCollection((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Collection ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteCollection((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Collection ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createCollection($data)
    {
        try {
            if (!isset($data['volunteer_id']) || !isset($data['vehicle_id'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new collection'];
            }

            $volunteer = $this->entityManager->find(UserModel::class, $data['volunteer_id']);
            if (!$volunteer) {
                http_response_code(404);
                return ['error' => 'Volunteer not found'];
            }

            $vehicle = $this->entityManager->find(VehicleModel::class, $data['vehicle_id']);
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }

            $collection = new CollectionModel();
            $collection->setVolunteer($volunteer);
            $collection->setVehicle($vehicle);
            $collection->setCollectionDate(new \DateTime("now"));
            $collection->setCreatedAt(new \DateTime("now"));
            $collection->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($collection);
            $this->entityManager->flush();

            return ['id' => $collection->getId(), 'message' => 'Collection created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createCollection: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCollection($id)
    {
        try {
            $collection = $this->entityManager->find(CollectionModel::class, $id);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }
            return $collection->jsonSerialize();
        } catch (\Exception $e) {
            error_log("Exception in getCollection: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateCollection($id, $data)
    {
        try {
            $collection = $this->entityManager->find(CollectionModel::class, $id);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            if (isset($data['volunteer_id'])) {
                $volunteer = $this->entityManager->find(UserModel::class, $data['volunteer_id']);
                if (!$volunteer) {
                    http_response_code(404);
                    return ['error' => 'Volunteer not found'];
                }
                $collection->setVolunteer($volunteer);
            }
            if (isset($data['vehicle_id'])) {
                $vehicle = $this->entityManager->find(VehicleModel::class, $data['vehicle_id']);
                if (!$vehicle) {
                    http_response_code(404);
                    return ['error' => 'Vehicle not found'];
                }
                $collection->setVehicle($vehicle);
            }
            $collection->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $collection->getId(), 'message' => 'Collection updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateCollection: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCollection($id)
    {
        try {
            $collection = $this->entityManager->find(CollectionModel::class, $id);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            $this->entityManager->remove($collection);
            $this->entityManager->flush();

            return ['message' => 'Collection deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteCollection: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllCollections()
    {
        try {
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);
            $collections = $collectionRepository->findAll();

            $serializedCollections = [];
            foreach ($collections as $collection) {
                $serializedCollections[] = $collection->jsonSerialize();
            }

            return $serializedCollections;
        } catch (\Exception $e) {
            error_log("Exception in getAllCollections: " . $e->getMessage());
            throw $e;
        }
    }
}
?>