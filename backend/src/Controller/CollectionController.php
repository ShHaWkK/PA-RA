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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CollectionController
{
    private $entityManager;
    private $excelService;
    private $emailService;


    public function __construct(EntityManager $entityManager, $excelService, $emailService)
    {
        $this->entityManager = $entityManager;
        $this->excelService = $excelService;
        $this->emailService = $emailService;
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                if ( isset($uriParts[2]) ) {
                    if($uriParts[2] === 'send_excel')
                    {
                        return $this->sendCollectionExcelEmail($uriParts[1],$uriParts[3]);
                    }
                    elseif ($uriParts[2] === 'export')
                    {
                        return $this->exportCollectionToExcel($uriParts[1]);
                    }
                }else {
                    return $this->createCollection($input);
                }
                case 'GET':
                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2])) {
                            if ($uriParts[2] === 'products') {
                                return $this->getProductsFromCollection((int) $uriParts[1]);
                            } elseif ($uriParts[2] === 'get_excel') {
                                return $this->getCollectionExcel($uriParts[1]);
                            } else {
                                http_response_code(400);
                                return ['error' => 'Invalid endpoint'];
                            }
                        } else {
                            return $this->getCollection((int) $uriParts[1]);
                        }
                    } else {
                        if(!empty($_GET)){
                            return $this->getCollectionsByCriteria($_GET);
                        }
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

            // Une fois les produits affectés, on génére le fichier excel pour la collecte
            $this->exportCollectionToExcel($collectionId);

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

            // Une fois les produits retirés, on regénére le fichier excel pour la collecte
            $this->exportCollectionToExcel($collectionId);

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

    public function getCollectionsByCriteria(array $queryParameters)
    {
        try {
            // Créez une instance de QueryBuilder
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);
            $queryBuilder = $collectionRepository->createQueryBuilder('c');

            // Filtrage par statut de complétion
            if (isset($queryParameters['completed'])) {
                $completed = filter_var($queryParameters['completed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($completed !== null) {
                    $queryBuilder->andWhere('c.is_completed = :completed')
                        ->setParameter('completed', $completed);
                } else {
                    error_log("Invalid completed parameter: " . $queryParameters['completed']);
                }
            }

            // Filtrage par date
            if (isset($queryParameters['date'])) {
                $date = $queryParameters['date'];
                $startDate = \DateTime::createFromFormat('Y-m-d', $date);
                if ($startDate) {
                    $startDate->setTime(0, 0, 0); // Début de la journée
                    $endDate = clone $startDate;
                    $endDate->setTime(23, 59, 59); // Fin de la journée

                    $queryBuilder->andWhere('c.collection_date >= :start_date')
                        ->andWhere('c.collection_date <= :end_date')
                        ->setParameter('start_date', $startDate)
                        ->setParameter('end_date', $endDate);
                } else {
                    error_log("Invalid date format: " . $date);
                }
            }

            // Exécutez la requête et récupérez les résultats
            $collections = $queryBuilder->getQuery()->getResult();

            // Vérifiez si des collections ont été trouvées
            if (empty($collections)) {
                http_response_code(404);
                return ['error' => 'No collections found with the given criteria'];
            }

            // Retournez les collections en utilisant jsonSerialize
            return array_map(function ($collection) {
                return $collection->jsonSerialize();
            }, $collections);
        } catch (\Exception $e) {
            error_log("Exception in getCollectionsByCriteria: " . $e->getMessage());
            throw $e;
        }
    }

    public function exportCollectionToExcel(int $collectionId): array
    {
        try {
            // Récupérer les données de la collection
            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            $collection_products = $this->getProductsFromCollection($collectionId);

            // Générer le fichier Excel et récupérer le chemin du fichier
            $excelFilePath = $this->excelService->generateCollectionExcel($collection_products);

            error_log("Excel file generated at path: " . $excelFilePath);

            $collection->setExcelPath($excelFilePath);
            $this->entityManager->flush();

            $recipient_email= $collection->getVolunteer()->getEmail();

            $this->sendCollectionExcelEmail($collectionId, $recipient_email);

            // Retourner un message de confirmation avec le chemin du fichier
            return ['message' => 'Excel file successfully generated.'];

        } catch (\Exception $e) {
            // Log l'erreur et retourner un message d'erreur
            error_log("Exception dans exportCollectionsToExcel: " . $e->getMessage());
            return [
                'message' => 'Une erreur est survenue lors de la génération du fichier Excel.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getCollectionExcel(int $collectionId): array
    {
        try {
            // Récupérer la collection
            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);

            if (!$collection) {
                http_response_code(404);
                return ["Collection with ID $collectionId not found."];
            }

            // Récupérer le chemin relatif du fichier Excel
            $relativeFilePath = $collection->getExcelPath();

            if (!$relativeFilePath) {
                http_response_code(400);
                return ["Invalid file path. No file path associated with collection ID $collectionId."];
            }

            // Obtenir le contenu du fichier et d'autres informations depuis le service
            $fileData = $this->excelService->getFileContent($relativeFilePath);

            if (isset($fileData['error'])) {
                http_response_code(404);
                return [$fileData['error']];
            }

            // Définir les headers pour le téléchargement
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileData['filename'] . '"');
            header('Content-Length: ' . $fileData['size']);

            // Envoyer le contenu du fichier
            echo $fileData['content'];
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            return ["An error occurred while retrieving the Excel file.", 'error' => $e->getMessage()];
        }
    }

    public function sendCollectionExcelEmail($collectionId, $recipientEmail)
    {
        try {
            // Récupérer la collection à partir de l'ID
            $collection = $this->entityManager->find(CollectionModel::class, $collectionId);
            if (!$collection) {
                http_response_code(404);
                throw new \Exception("Collection with ID $collectionId not found.");
            }

            if($recipientEmail === null){
                $recipientEmail = $collection->getVolunteer()->getEmail();
            }


            // Obtenir le chemin relatif du fichier Excel associé à la collection
            $relativeFilePath = $collection->getExcelPath();

            // Obtenir le contenu du fichier et d'autres informations depuis le service
            $fileData = $this->excelService->getFileContent($relativeFilePath);

            // Vérifier si le contenu du fichier est disponible
            if (!isset($fileData['content']) || empty($fileData['content'])) {
                http_response_code(404);
                throw new \Exception("Excel file content not found or empty at path: $relativeFilePath.");
            }

            // Obtenir et formater la date de la collection
            $collectionDate = $collection->getCollectionDate();
            if (!$collectionDate) {
                http_response_code(404);
                throw new \Exception("Collection date is not set.");
            }
            $formattedDate = $collectionDate->format('d-m-Y');

            // Préparer le sujet et le corps de l'e-mail
            $subject = "Votre fichier Excel collecte du {$formattedDate}";
            $body = "Ci-joint votre fichier excel de collecte du {$formattedDate}.";

            // Obtenir le nom du fichier
            $fileName = basename($relativeFilePath);

            // Envoyer le fichier Excel en pièce jointe au destinataire
            $result = $this->emailService->sendExcelFile($recipientEmail, $subject, $body, $fileName, $fileData['content']);

            // Vérifier le résultat de l'envoi d'e-mail
            if (!$result) {
                http_response_code(400);
                throw new \Exception("Failed to send email to $recipientEmail.");
            }

            return ['message' => 'Mail successfully sent'];
        } catch (\Exception $e) {
            // Gérer les exceptions et enregistrer les erreurs
            error_log("Exception in sendCollectionExcelEmail: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

}
?>