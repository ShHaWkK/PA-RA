<?php
// Path: backend/src/Controller/CollectionController.php
namespace Controller;

use Entity\CollectionModel;
use Entity\CollectionProductModel;
use Entity\VehicleModel;
use Entity\UserModel;
use Entity\ProductNotificationModel;
use Doctrine\ORM\EntityManager;
use Service\ExcelService;
use Doctrine\ORM\EntityNotFoundException;

class CollectionController
{
    private $entityManager;
    private $excelService;


    public function __construct(EntityManager $entityManager, $excelService)
    {
        $this->entityManager = $entityManager;
        $this->excelService = $excelService;
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createCollection($input);
                case 'GET':
                    // Vérifier si des paramètres "completed" et éventuellement "date" sont passés dans la requête
                    if (isset($_GET['completed'])) {
                        // Convertir le paramètre "completed" en booléen
                        $completed = filter_var($_GET['completed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        if ($completed === null) {
                            http_response_code(400);
                            return ['error' => 'Invalid completed status'];
                        }

                        // Récupérer la date si elle est fournie
                        $date = $_GET['date'] ?? null;

                        // Appeler la fonction pour récupérer les collections par date et complétion
                        return $this->getCollectionsByDateAndCompletion($date, $completed);
                    }

                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2])) {
                            if ($uriParts[2] === 'products') {
                                return $this->getProductsFromCollection((int) $uriParts[1]);
                            }
                            elseif ( $uriParts[2] === 'export') {
                                return $this->exportCollectionToExcel($uriParts[1]);
                            }
                            elseif ($uriParts[2] === 'by-date') {
                                return $this->getCollectionsByDate($uriParts[1]);
                            } elseif ($uriParts[2] === 'by-completion') {
                                if ($uriParts[1] === 'true') {
                                    $completed = true;
                                } elseif ($uriParts[1] === 'false') {
                                    $completed = false;
                                } else {
                                    http_response_code(400);
                                    return ['error' => 'Invalid completion status'];
                                }
                                return $this->getCollectionsByCompletion($completed);
                            } else {
                                http_response_code(400);
                                return ['error' => 'Invalid endpoint'];
                            }
                        } else {
                            return $this->getCollection((int) $uriParts[1]);
                        }
                    } else {
                        return $this->getAllCollections();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2])) {
                            return $this->assignProduct((int) $uriParts[1], $input['products']);
                        } else {
                            return $this->updateCollection((int) $uriParts[1], $input);
                        }
                    }
                    http_response_code(400);
                    return ['error' => 'Collection ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteCollection((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Collection ID not specified'];
                case 'PATCH':
                    if (isset($uriParts[1])) {
                        if (isset($input['products'])) {
                            if (isset($uriParts[2]) && $uriParts[2] === 'remove') {
                                return $this->removeProductsFromCollection((int) $uriParts[1], $input['products']);
                            } elseif (isset($uriParts[2]) && $uriParts[2] === 'update') {
                                return $this->modifyProduct((int) $uriParts[1], $input['products']);
                            }else
                            {
                                return $this->assignProduct((int) $uriParts[1], $input['products']);
                            }
                        }
                        http_response_code(400);
                        return ['error' => 'Products not specified'];
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

            if (isset($data['is_completed'])){
                $collection->setIsCompleted($data['vehicle_id']);
            }

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
                if (!isset($data['notification_id']) ) {
                    http_response_code(400);
                    return ['error' => 'Missing required fields for product assignment'];
                }

                $productNotification = $this->entityManager->find(ProductNotificationModel::class, $data['notification_id']);
                if (!$productNotification) {
                    http_response_code(404);
                    return ['error' => 'ProductNotification not found'];
                }
                $productNotification->setIsAssigned(true);

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
                if(isset($data['quantity_collected'])) {
                    $collectionProduct->setQuantityCollected($data['quantity_collected']);
                }

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

    public function modifyProduct(int $collectionId, array $products)
    {
        $this->entityManager->beginTransaction();
        try {
            // Vérifier les données
            if (empty($products)) {
                http_response_code(400);
                return ['error' => 'No products provided for modification'];
            }

            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            foreach ($products as $data) {
                if (!isset($data['notification_id'])) {
                    http_response_code(400);
                    return ['error' => 'Missing notification_id for product modification'];
                }

                $productNotification = $this->entityManager->find(ProductNotificationModel::class, $data['notification_id']);
                if (!$productNotification) {
                    http_response_code(404);
                    return ['error' => 'ProductNotification not found'];
                }

                // Rechercher l'association existante
                $existingAssociation = $this->entityManager->getRepository(CollectionProductModel::class)
                    ->findOneBy([
                        'collection' => $collection,
                        'notification' => $productNotification
                    ]);

                if (!$existingAssociation) {
                    http_response_code(404);
                    return ['error' => 'Product not assigned to this collection'];
                }

                // Modifier les champs de l'association existante
                if (isset($data['quantity_collected'])) {
                    $existingAssociation->setQuantityCollected($data['quantity_collected']);
                }

                if (isset($data['is_collected'])) {
                    $existingAssociation->setIsCollected($data['is_collected']);
                }

                $this->entityManager->persist($existingAssociation);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return ['message' => 'Product(s) modified successfully'];
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            error_log("Exception in modifyProduct: " . $e->getMessage());
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
                $productNotification->setIsAssigned(false);

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

    public function getProductsFromCollection(int $collectionId)
    {
        try {
            // Récupérer la collecte par son ID
            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            if (!$collection) {
                http_response_code(404);
                return ['error' => 'Collection not found'];
            }

            // Récupérer tous les produits associés à la collecte
            $collectionProducts = $this->entityManager->getRepository(CollectionProductModel::class)
                ->findBy(['collection' => $collection]);

            if (empty($collectionProducts)) {
                return ['message' => 'No products found for this collection'];
            }

            // Utiliser jsonSerialize pour formater les résultats en JSON
            $products = array_map(fn($collectionProduct) => $collectionProduct->jsonSerialize(), $collectionProducts);

            return $products;
        } catch (\Exception $e) {
            error_log("Exception in getProductsFromCollection: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCollectionsByDate(string $date)
    {
        try {
            // Créez un objet DateTime pour le début de la journée
            $startOfDay = new \DateTime($date . ' 00:00:00');
            // Créez un objet DateTime pour la fin de la journée
            $endOfDay = new \DateTime($date . ' 23:59:59');

            // Créez une instance de QueryBuilder
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);
            $queryBuilder = $collectionRepository->createQueryBuilder('c')
                ->where('c.collection_date >= :start')
                ->andWhere('c.collection_date <= :end')
                ->setParameter('start', $startOfDay)
                ->setParameter('end', $endOfDay);

            // Exécutez la requête et récupérez les résultats
            $collections = $queryBuilder->getQuery()->getResult();

            // Retournez les collections en utilisant jsonSerialize
            return array_map(function ($collection) {
                return $collection->jsonSerialize();
            }, $collections);
        } catch (\Exception $e) {
            error_log("Exception in getCollectionsByDate: " . $e->getMessage());
            throw $e;
        }
    }

    private function getCollectionsByCompletion(bool $completed)
    {
        try {
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);
            $collections = $collectionRepository->createQueryBuilder('c')
                ->where('c.is_completed = :completed')
                ->setParameter('completed', $completed)
                ->getQuery()
                ->getResult();

            if (empty($collections)) {
                http_response_code(404);
                return ['error' => 'No collections found for the specified completion status'];
            }

            $serializedCollections = [];
            foreach ($collections as $collection) {
                $serializedCollections[] = $collection->jsonSerialize();
            }

            return $serializedCollections;
        } catch (\Exception $e) {
            error_log("Exception in getCollectionsByCompletion: " . $e->getMessage());
            throw $e;
        }
    }

    private function getCollectionsByDateAndCompletion(?string $date, bool $completed)
    {
        try {
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);
            $queryBuilder = $collectionRepository->createQueryBuilder('c')
                ->where('c.is_completed = :completed')
                ->setParameter('completed', $completed);

            // Si la date est fournie, ajoutez le filtre de date
            if ($date !== null) {
                $startOfDay = new \DateTime($date . ' 00:00:00');
                $endOfDay = new \DateTime($date . ' 23:59:59');
                $queryBuilder->andWhere('c.collection_date >= :start')
                    ->andWhere('c.collection_date <= :end')
                    ->setParameter('start', $startOfDay)
                    ->setParameter('end', $endOfDay);
            }

            // Exécutez la requête et récupérez les résultats
            $collections = $queryBuilder->getQuery()->getResult();

            // Retournez les collections en utilisant jsonSerialize
            return array_map(function ($collection) {
                return $collection->jsonSerialize();
            }, $collections);
        } catch (\Exception $e) {
            error_log("Exception in getCollectionsByDateAndCompletion: " . $e->getMessage());
            throw $e;
        }
    }

    public function exportCollectionToExcel(int $collectionId)
    {
        try {
            // Retrieve the collection data
            $collection = $this->getProductsFromCollection($collectionId);

            // Generate the Excel file
            $excelFilePath = $this->excelService->generateCollectionExcel($collection);

            // Set headers for file download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="collection_' . uniqid() . '.xlsx"');
            header('Content-Length: ' . filesize($excelFilePath));

            // Read the file and output it to the browser
            readfile($excelFilePath);

            // Clean up: delete the temporary file
            unlink($excelFilePath);

            exit;
        } catch (\Exception $e) {
            error_log("Exception in exportCollectionsToExcel: " . $e->getMessage());
            throw $e;
        }
    }

}
?>