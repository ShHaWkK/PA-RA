<?php
// Path: backend/src/Controller/CollectionController.php
namespace Controller;

use Entity\CollectionModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;

class CollectionController
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
            // Validate input data (add your own validation logic)
            if (!isset($data['company_id']) || !isset($data['product_id'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new collection'];
            }

            $collection = new CollectionModel();
            $collection->setCompanyId($data['company_id']);
            $collection->setProductId($data['product_id']);
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
            return json_decode($this->serializer->serialize($collection, 'json'), true);
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

            if (isset($data['company_id'])) {
                $collection->setCompanyId($data['company_id']);
            }
            if (isset($data['product_id'])) {
                $collection->setProductId($data['product_id']);
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
            return json_decode($this->serializer->serialize($collections, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllCollections: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
