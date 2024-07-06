<?php
// Path: backend/src/Controller/StockController.php
namespace Controller;

use Entity\StockModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class StockController
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
        switch ($method) {
            case 'POST':
                return $this->createStock($input);
            case 'GET':
                if (isset($uriParts[1])) {
                    return $this->getStock((int) $uriParts[1]);
                } else {
                    return $this->getAllStocks();
                }
            case 'PUT':
                if (isset($uriParts[1])) {
                    return $this->updateStock((int) $uriParts[1], $input);
                }
                http_response_code(400);
                return ['error' => 'Stock ID not specified'];
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteStock((int) $uriParts[1]);
                }
                http_response_code(400);
                return ['error' => 'Stock ID not specified'];
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function createStock($data)
    {
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for new stock'];
        }

        $stock = new StockModel();
        $stock->setProductId($data['product_id']);
        $stock->setQuantity($data['quantity']);
        $stock->setEntryDate(new \DateTime("now"));
        $stock->setCreatedAt(new \DateTime("now"));
        $stock->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return ['id' => $stock->getId(), 'message' => 'Stock created successfully'];
    }

    public function getStock($id)
    {
        $stock = $this->entityManager->find(StockModel::class, $id);
        if (!$stock) {
            http_response_code(404);
            return ['error' => 'Stock not found'];
        }
        return json_decode($this->serializer->serialize($stock, 'json'), true);
    }

    public function updateStock($id, $data)
    {
        $stock = $this->entityManager->find(StockModel::class, $id);
        if (!$stock) {
            http_response_code(404);
            return ['error' => 'Stock not found'];
        }

        if (isset($data['product_id'])) {
            $stock->setProductId($data['product_id']);
        }
        if (isset($data['quantity'])) {
            $stock->setQuantity($data['quantity']);
        }
        if (isset($data['entry_date'])) {
            $stock->setEntryDate(new \DateTime($data['entry_date']));
        }
        if (isset($data['exit_date'])) {
            $stock->setExitDate(new \DateTime($data['exit_date']));
        }
        $stock->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return ['id' => $stock->getId(), 'message' => 'Stock updated successfully'];
    }

    public function deleteStock($id)
    {
        $stock = $this->entityManager->find(StockModel::class, $id);
        if (!$stock) {
            http_response_code(404);
            return ['error' => 'Stock not found'];
        }

        $this->entityManager->remove($stock);
        $this->entityManager->flush();

        return ['message' => 'Stock deleted successfully'];
    }

    public function getAllStocks()
    {
        $stockRepository = $this->entityManager->getRepository(StockModel::class);
        $stocks = $stockRepository->findAll();
        return json_decode($this->serializer->serialize($stocks, 'json'), true);
    }
}
?>
