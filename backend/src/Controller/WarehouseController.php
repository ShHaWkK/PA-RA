<?php
namespace Controller;

use Entity\WarehouseModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;

class WarehouseController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processRequest($method, $uriParts, $input)
    {
        switch ($method) {
            case 'POST':
                return $this->createWarehouse($input);
            case 'GET':
                if (isset($uriParts[1])) {
                    if (isset($uriParts[2])) {
                        return $this->getWarehouseCapacity($uriParts[1]);
                    } else {
                        return $this->getWarehouse((int) $uriParts[1]);
                    }
                } else {
                    return $this->getAllWarehouses();
                }
            case 'PUT':
                if (isset($uriParts[1])) {
                    return $this->updateWarehouse((int) $uriParts[1], $input);
                }
                http_response_code(400);
                return ['error' => 'Warehouse ID not specified'];
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteWarehouse((int) $uriParts[1]);
                }
                http_response_code(400);
                return ['error' => 'Warehouse ID not specified'];
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function createWarehouse($data)
    {
        if (!isset($data['name']) || !isset($data['address']) || !isset($data['capacity']) || !isset($data['city']) || !isset($data['country'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for new warehouse'];
        }

        if (!is_numeric($data['capacity'])) {
            http_response_code(400);
            return ['error' => 'Capacity must be a number'];
        }

        if ($data['capacity'] <= 0) {
            http_response_code(400);
            return ['error' => 'Capacity must be greater than 0'];
        }

        if (!is_string($data['name']) || !is_string($data['address']) || !is_string($data['city']) || !is_string($data['country'])) {
            http_response_code(400);
            return ['error' => 'Name, address, city, and country must be strings'];
        }

        if (isset($data['contact_info']) && !is_string($data['contact_info'])) {
            http_response_code(400);
            return ['error' => 'Contact info must be a string'];
        }

        if (strlen($data['name']) > 255 || strlen($data['address']) > 255 || strlen($data['city']) > 255 || strlen($data['country']) > 255) {
            http_response_code(400);
            return ['error' => 'Name, address, city, and country must be less than 255 characters'];
        }

        $warehouse = new WarehouseModel();
        $warehouse->setName($data['name']);
        $warehouse->setAddress($data['address']);
        $warehouse->setContactInfo($data['contact_info'] ?? null);
        $warehouse->setCapacity($data['capacity']);
        $warehouse->setCity($data['city']);
        $warehouse->setCountry($data['country']);
        $warehouse->setCreatedAt(new \DateTime("now"));
        $warehouse->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return ['id' => $warehouse->getId(), 'message' => 'Warehouse created successfully'];
    }

    public function getWarehouse($id)
    {
        $warehouse = $this->entityManager->find(WarehouseModel::class, $id);
        if (!$warehouse) {
            http_response_code(404);
            return ['error' => 'Warehouse not found'];
        }

        $data = $warehouse->jsonSerialize();
        return $data;
    }

    public function updateWarehouse($id, $data)
    {
        $warehouse = $this->entityManager->find(WarehouseModel::class, $id);
        if (!$warehouse) {
            http_response_code(404);
            return ['error' => 'Warehouse not found'];
        }

        if (isset($data['name'])) {
            $warehouse->setName($data['name']);
        }
        if (isset($data['address'])) {
            $warehouse->setAddress($data['address']);
        }
        if (isset($data['contact_info'])) {
            $warehouse->setContactInfo($data['contact_info']);
        }
        if (isset($data['capacity'])) {
            $warehouse->setCapacity($data['capacity']);
        }
        if (isset($data['city'])) {
            $warehouse->setCity($data['city']);
        }
        if (isset($data['country'])) {
            $warehouse->setCountry($data['country']);
        }
        $warehouse->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return ['id' => $warehouse->getId(), 'message' => 'Warehouse updated successfully'];
    }

    public function deleteWarehouse($id)
    {
        $warehouse = $this->entityManager->find(WarehouseModel::class, $id);
        if (!$warehouse) {
            http_response_code(404);
            return ['error' => 'Warehouse not found'];
        }

        $this->entityManager->remove($warehouse);
        $this->entityManager->flush();

        return ['message' => 'Warehouse deleted successfully'];
    }

    public function getAllWarehouses()
    {
        $warehouseRepository = $this->entityManager->getRepository(WarehouseModel::class);
        $warehouses = $warehouseRepository->findAll();

        // Préparer les données en utilisant jsonSerialize()
        $serializedWarehouses = [];
        foreach ($warehouses as $warehouse) {
            $serializedWarehouses[] = $warehouse->jsonSerialize();
        }

        return $serializedWarehouses;
    }

    public function getWarehouseCapacity($id)
    {
        $warehouse = $this->entityManager->find(WarehouseModel::class, $id);
        if (!$warehouse) {
            http_response_code(404);
            return ['error' => 'Warehouse not found'];
        }

        $stocks = $warehouse->getStocks();

        // Calculer la capacité occupée
        $occupiedCapacity = 0;
        foreach ($stocks as $stock) {
            $product = $stock->getProduct();
            if ($product) {
                $occupiedCapacity += $stock->getQuantity() * $product->getVolume();
            }
        }

        $totalCapacity = $warehouse->getCapacity();

        return [
            'total_capacity' => $totalCapacity,
            'occupied_capacity' => $occupiedCapacity,
            'available_capacity' => $totalCapacity - $occupiedCapacity
        ];
    }
}