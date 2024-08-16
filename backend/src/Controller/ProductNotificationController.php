<?php
// Path: backend/src/Controller/ProductNotificationController.php
namespace Controller;

use Entity\ProductNotificationModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ProductNotificationController
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

    public function processRequest(string $method, array $uriParts, ?array $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createProductNotification($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getProductNotification((int) $uriParts[1]);
                    } else {
                        return $this->getAllProductNotifications($_GET);
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateProductNotification((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'ProductNotification ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteProductNotification((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'ProductNotification ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createProductNotification(array $data)
    {
        try {
            if (!isset($data['company_id']) || !isset($data['product_id']) || !isset($data['notified_quantity']) || !isset($data['address']) || !isset($data['wished_collection_date'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new product notification'];
            }

            $productNotification = new ProductNotificationModel();
            $productNotification->setCompanyId($data['company_id'], $this->entityManager);
            $productNotification->setProductId($data['product_id'], $this->entityManager);
            $productNotification->setNotifiedQuantity($data['notified_quantity']);
            $productNotification->setAddress($data['address']);
            $productNotification->setWishedCollectionDate(new \DateTime($data['wished_collection_date']));
            $productNotification->setNotifiedAt(new \DateTime("now"));

            $this->entityManager->persist($productNotification);
            $this->entityManager->flush();

            return ['id' => $productNotification->getId(), 'message' => 'ProductNotification created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createProductNotification: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProductNotification(int $id)
    {
        try {
            $productNotification = $this->entityManager->find(ProductNotificationModel::class, $id);
            if (!$productNotification) {
                http_response_code(404);
                return ['error' => 'ProductNotification not found'];
            }
            return json_decode($this->serializer->serialize($productNotification, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getProductNotification: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProductNotification(int $id, array $data)
    {
        try {
            $productNotification = $this->entityManager->find(ProductNotificationModel::class, $id);
            if (!$productNotification) {
                http_response_code(404);
                return ['error' => 'ProductNotification not found'];
            }

            if (isset($data['company_id'])) {
                $productNotification->setCompanyId($data['company_id'], $this->entityManager);
            }
            if (isset($data['product_id'])) {
                $productNotification->setProductId($data['product_id'], $this->entityManager);
            }
            if (isset($data['notified_quantity'])) {
                $productNotification->setNotifiedQuantity($data['notified_quantity']);
            }
            if (isset($data['address'])) {
                $productNotification->setAddress($data['address']);
            }
            if (isset($data['wished_collection_date'])) {
                $productNotification->setWishedCollectionDate(new \DateTime($data['wished_collection_date']));
            }
            $productNotification->setNotifiedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $productNotification->getId(), 'message' => 'ProductNotification updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateProductNotification: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProductNotification(int $id)
    {
        try {
            $productNotification = $this->entityManager->find(ProductNotificationModel::class, $id);
            if (!$productNotification) {
                http_response_code(404);
                return ['error' => 'ProductNotification not found'];
            }

            $this->entityManager->remove($productNotification);
            $this->entityManager->flush();

            return ['message' => 'ProductNotification deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteProductNotification: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllProductNotifications(?array $queryParams)
    {
        try {
            $productNotificationRepository = $this->entityManager->getRepository(ProductNotificationModel::class);

            // Création d'un QueryBuilder
            $qb = $productNotificationRepository->createQueryBuilder('p');

            // Ajout des conditions pour la date et l'adresse
            if (isset($queryParams['date'])) {
                $qb->andWhere('p.wished_collection_date = :date')
                    ->setParameter('date', new \DateTime($queryParams['date']));
            }
            if (isset($queryParams['address'])) {
                $qb->andWhere('p.address = :address')
                    ->setParameter('address', $queryParams['address']);
            }

            // Vérification du champ 'is_assigned' dans les paramètres
            if (isset($queryParams['is_assigned'])) {
                $isAssigned = (bool)$queryParams['is_assigned'];
                $qb->andWhere('p.is_assigned = :is_assigned')
                    ->setParameter('is_assigned', $isAssigned);
            }

            // Exécution de la requête
            $productNotifications = $qb->getQuery()->getResult();

            // Sérialisation des résultats en JSON
            return json_decode($this->serializer->serialize($productNotifications, 'json'), true);
        } catch (\Exception $e) {
            // Journalisation des erreurs
            error_log("Exception in getAllProductNotifications: " . $e->getMessage());
            throw $e;
        }
    }

}
?>