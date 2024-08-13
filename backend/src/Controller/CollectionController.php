<?php
// Path: backend/src/Controller/CollectionController.php
namespace Controller;

use Entity\CollectionModel;
use Entity\CollectionProductModel;
use Entity\VehicleModel;
use Entity\UserModel;
use Entity\ProductNotificationModel;
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
                    if (isset($uriParts[1]) && !isset($uriParts[2])) {
                        return $this->updateCollection((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Invalid request for PUT method'];
                case 'PATCH':
                    if (isset($uriParts[1])) {
                        if (isset($input['products'])) {
                            if (isset($uriParts[2]) && $uriParts[2] === 'remove') {
                                return $this->removeProductsFromCollection((int) $uriParts[1], $input['products']);
                            } else {
                                return $this->assignProduct((int) $uriParts[1], $input['products']);
                            }
                        }
                        http_response_code(400);
                        return ['error' => 'Products not specified'];
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

    public function assignProduct(int $collectionId, array $products)
    {
        $this->entityManager->beginTransaction();
        try {
            // Vérifier les données
            if (empty($products)) {
                http_response_code(400);
                return ['error' => 'No products provided for assignment'];
            }

            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            foreach ($products as $data) {
                if (!isset($data['notification_id']) || !isset($data['quantity_collected'])) {
                    http_response_code(400);
                    return ['error' => 'Missing required fields for product assignment'];
                }

                $productNotification = $this->entityManager->find(ProductNotificationModel::class, $data['notification_id']);
                if (!$productNotification) {
                    http_response_code(404);
                    return ['error' => 'ProductNotification not found'];
                }

                // Vérifier si l'association existe déjà
                $existingAssociation = $this->entityManager->getRepository(CollectionProductModel::class)
                    ->findOneBy([
                        'collection' => $collection,
                        'notification' => $productNotification
                    ]);

                if ($existingAssociation) {
                    http_response_code(409); // Conflit
                    return ['error' => 'Product already assigned to this collection'];
                }

                // Assigner le produit à la collecte
                $collectionProduct = new CollectionProductModel();
                $collectionProduct->setCollection($collection);
                $collectionProduct->setNotification($productNotification);
                $collectionProduct->setQuantityCollected($data['quantity_collected']);

                $this->entityManager->persist($collectionProduct);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return ['message' => 'Products assigned to collection successfully'];
        } catch (UniqueConstraintViolationException $e) {
            $this->entityManager->rollback();
            http_response_code(409); // Conflit
            return ['error' => 'Product assignment already exists'];
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            error_log("Exception in assignProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeProductsFromCollection(int $collectionId, array $products)
    {
        $this->entityManager->beginTransaction();
        try {
            if (empty($products)) {
                http_response_code(400);
                return ['error' => 'No products provided for removal'];
            }

            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            foreach ($products as $data) {
                if (!isset($data['notification_id'])) {
                    http_response_code(400);
                    return ['error' => 'Missing required fields for product removal'];
                }

                $productNotification = $this->entityManager->find(ProductNotificationModel::class, $data['notification_id']);
                if (!$productNotification) {
                    http_response_code(404);
                    return ['error' => 'ProductNotification not found'];
                }

                // Trouver et supprimer l'association
                $collectionProduct = $this->entityManager->getRepository(CollectionProductModel::class)
                    ->findOneBy([
                        'collection' => $collection,
                        'notification' => $productNotification
                    ]);

                if (!$collectionProduct) {
                    http_response_code(404);
                    return ['error' => 'Product not found in this collection'];
                }

                $this->entityManager->remove($collectionProduct);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return ['message' => 'Products removed from collection successfully'];
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            error_log("Exception in removeProductsFromCollection: " . $e->getMessage());
            throw $e;
        }
    }
}
?>