<?php
namespace Controller;

use Entity\StockModel;
use Entity\ProductModel;
use Entity\WarehouseModel;
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
                    switch ($uriParts[1]){
                        case 'getStocksByWarehouse':
                            if (isset($uriParts[2])) {
                                return $this->getStockByWarehouse($uriParts[2]);
                            }else{
                                http_response_code(400);
                                return ['error' => 'Warehouse ID not specified'];
                            }
                            break;
                        default:
                            return $this->getStock((int) $uriParts[1]);
                    }

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
        if (!isset($data['product_id']) || !isset($data['quantity']) || !isset($data['availability']) || !isset($data['warehouse_id'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for new stock'];
        }

        $warehouse = $this->entityManager->find(WarehouseModel::class, $data['warehouse_id']);
        if (!$warehouse) {
            http_response_code(400);
            return ['error' => 'Warehouse not found'];
        }

        $product = $this->entityManager->find(ProductModel::class, $data['product_id']);
        if (!$product) {
            http_response_code(400);
            return ['error' => 'Product not found'];
        }

        $currentStockVolume = $this->getCurrentWarehouseStockVolume($data['warehouse_id']);
        $availableCapacity = $warehouse->getCapacity() - $currentStockVolume;
        $requiredCapacity = $product->getVolume() * $data['quantity'];

        // Debugging output
        error_log("Warehouse ID: " . $warehouse->getId());
        error_log("Warehouse Capacity: " . $warehouse->getCapacity());
        error_log("Current Warehouse Stock Volume: " . $currentStockVolume);
        error_log("Product Volume: " . $product->getVolume());
        error_log("Required Capacity for New Stock: " . $requiredCapacity);
        error_log("Available Capacity: " . $availableCapacity);

        if ($availableCapacity < $requiredCapacity) {
            http_response_code(400);
            return ['error' => 'Not enough capacity in the warehouse'];
        }

        $stock = new StockModel();
        $stock->setProductId($data['product_id']);
        $stock->setQuantity($data['quantity']);
        $stock->setAvailability($data['availability']);
        $stock->setWarehouseId($data['warehouse_id']);
        $stock->setEntryDate(new \DateTime("now"));
        $stock->setCreatedAt(new \DateTime("now"));
        $stock->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return ['id' => $stock->getId(), 'message' => 'Stock created successfully'];
    }

    private function getCurrentWarehouseStockVolume($warehouseId)
    {
        $stocks = $this->entityManager->getRepository(StockModel::class)->findBy(['warehouse_id' => $warehouseId]);
        $currentVolume = 0;
        foreach ($stocks as $stock) {
            $product = $this->entityManager->find(ProductModel::class, $stock->getProductId());
            if ($product) {
                $currentVolume += $product->getVolume() * $stock->getQuantity();
            }
        }
        return $currentVolume;
    }

    public function getAllStocks()
    {
        $stocks = $this->entityManager->getRepository(StockModel::class)->findAll();
        $data = [];
        foreach ($stocks as $stock) {
            $data[] = $this->serializer->normalize($stock);
        }
        return $data;
    }

    public function getStock(int $id)
    {
        $stock = $this->entityManager->find(StockModel::class, $id);
        if (!$stock) {
            http_response_code(404);
            return ['error' => 'Stock not found'];
        }

        $product = $this->entityManager->find(ProductModel::class, $stock->getProductId());
        $productName = $product ? $product->getName() : null;

        $normalizedStock = [
            'id' => $stock->getId(),
            'product_id' => $stock->getProductId(),
            'product_name' => $productName,
            'quantity' => $stock->getQuantity(),
            'volume' => $stock->getQuantity() * $product->getVolume(),
            'entry_date' => $stock->getEntryDate()->format(\DateTime::ISO8601),
            'exit_date' => $stock->getExitDate() ? $stock->getExitDate()->format(\DateTime::ISO8601) : null,
            'availability' => $stock->getAvailability(),
            'created_at' => $stock->getCreatedAt()->format(\DateTime::ISO8601),
            'updated_at' => $stock->getUpdatedAt()->format(\DateTime::ISO8601),
            'warehouse_id' => $stock->getWarehouseId(),
        ];

        return $normalizedStock;
    }


    public function getStockByWarehouse(int $warehouseId)
    {
        $stocks = $this->entityManager->getRepository(StockModel::class)->findBy(['warehouse_id' => $warehouseId]);
        if (empty($stocks)) {
            http_response_code(404);
            return ['error' => 'No stocks found for this warehouse'];
        }

        $data = [];
        foreach ($stocks as $stock) {
            $product = $this->entityManager->find(ProductModel::class, $stock->getProductId());
            $productName = $product ? $product->getName() : null;

            $data[] = [
                'id' => $stock->getId(),
                'product_id' => $stock->getProductId(),
                'product_name' => $productName,
                'quantity' => $stock->getQuantity(),
                'volume' => $stock->getQuantity() * $product->getVolume(),
                'entry_date' => $stock->getEntryDate()->format(\DateTime::ISO8601),
                'exit_date' => $stock->getExitDate() ? $stock->getExitDate()->format(\DateTime::ISO8601) : null,
                'availability' => $stock->getAvailability(),
                'created_at' => $stock->getCreatedAt()->format(\DateTime::ISO8601),
                'updated_at' => $stock->getUpdatedAt()->format(\DateTime::ISO8601),
                'warehouse_id' => $stock->getWarehouseId(),
            ];
        }

        return $data;
    }

    public function updateStock(int $id, $data)
    {
        $stock = $this->entityManager->find(StockModel::class, $id);
        if (!$stock) {
            http_response_code(404);
            return ['error' => 'Stock not found'];
        }

        if (isset($data['quantity'])) {
            $stock->setQuantity($data['quantity']);
        }
        if (isset($data['availability'])) {
            $stock->setAvailability($data['availability']);
        }
        if (isset($data['warehouse_id'])) {
            $warehouse = $this->entityManager->find(WarehouseModel::class, $data['warehouse_id']);
            if (!$warehouse) {
                http_response_code(400);
                return ['error' => 'Warehouse not found'];
            }
            $stock->setWarehouseId($data['warehouse_id']);
        }
        if (isset($data['product_id'])) {
            $product = $this->entityManager->find(ProductModel::class, $data['product_id']);
            if (!$product) {
                http_response_code(400);
                return ['error' => 'Product not found'];
            }
            $stock->setProductId($data['product_id']);
        }

        $stock->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return ['message' => 'Stock updated successfully'];
    }

    public function deleteStock(int $id)
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
}
?>