<?php
namespace Controller;

use Entity\ProductModel;
use Entity\StockModel;
use Entity\WarehouseModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ProductController
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
                    return $this->createProduct($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if(isset($uriParts[2])){
                            return $this->getProductByID($uriParts[2]);
                        }
                        return $this->getProductByBarcode($uriParts[1]);
                    } else {
                        return $this->getAllProducts();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateProduct($uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Product barcode not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteProduct($uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Product barcode not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createProduct($data)
    {
        try {
            // Vérification des champs obligatoires
            if (!isset($data['name']) || !isset($data['barcode']) || !isset($data['expiration_date']) || !isset($data['volume']) || !isset($data['warehouse_id'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new product'];
            }
    
            // Vérification de l'existence du produit
            $existingProduct = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $data['barcode']]);
            if ($existingProduct) {
                http_response_code(400);
                return ['error' => 'Product with this barcode already exists'];
            }
    
            // Vérification de l'existence de l'entrepôt
            $warehouse = $this->entityManager->getRepository(WarehouseModel::class)->find($data['warehouse_id']);
            if (!$warehouse) {
                http_response_code(400);
                return ['error' => 'Warehouse not found'];
            }
    
            // Création du produit
            $product = new ProductModel();
            $product->setName($data['name']);
            $product->setBarcode($data['barcode']);
            $product->setExpirationDate(new \DateTime($data['expiration_date']));
            $product->setVolume($data['volume']);
            $product->setCreatedAt(new \DateTime("now"));
            $product->setUpdatedAt(new \DateTime("now"));
    
            // Génération et sauvegarde du QR Code
            $qrCode = new QrCode(json_encode([
                'name' => $data['name'],
                'barcode' => $data['barcode'],
                'expiration_date' => $data['expiration_date'],
                'volume' => $data['volume']
            ]));
            $qrCode->setSize(300);
            $qrCode->setMargin(10);
    
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
    
            $qrCodeDir = __DIR__ . '/../../public/qrcodes';
            if (!is_dir($qrCodeDir)) {
                if (!mkdir($qrCodeDir, 0777, true)) {
                    throw new \Exception("Failed to create directory: $qrCodeDir");
                }
            }
    
            $qrCodePath = '/qrcodes/' . $data['barcode'] . '.png';
            $fullPath = $qrCodeDir . '/' . $data['barcode'] . '.png';
            if (file_put_contents($fullPath, $result->getString()) === false) {
                throw new \Exception("Failed to write QR code to file: $fullPath");
            }
            $product->setQrCodePath($qrCodePath);
    
            // Sauvegarde du produit
            $this->entityManager->persist($product);
            $this->entityManager->flush();
    
            // Création de l'entrée de stock
            $stock = new StockModel();
            $stock->setProductId($product->getId());
            $stock->setQuantity(1);
            $stock->setAvailability('available');
            $stock->setWarehouse($warehouse);  // Associer l'objet WarehouseModel directement
            $stock->setEntryDate(new \DateTime("now"));
            $stock->setCreatedAt(new \DateTime("now"));
            $stock->setUpdatedAt(new \DateTime("now"));
    
            $this->entityManager->persist($stock);
            $this->entityManager->flush();
    
            return ['id' => $product->getId(), 'message' => 'Product created and stock entry added successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createProduct: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    
    
    private function getCurrentWarehouseStockVolume($warehouseId)
    {
        // Recherchez d'abord l'entité WarehouseModel en utilisant l'ID fourni
        $warehouse = $this->entityManager->getRepository(WarehouseModel::class)->find($warehouseId);
    
        if (!$warehouse) {
            throw new \Exception("Warehouse not found");
        }
    
        // Ensuite, récupérez tous les stocks associés à cet entrepôt
        $stocks = $warehouse->getStocks(); // Utilisation de la relation définie dans WarehouseModel
        $currentVolume = 0;
        
        foreach ($stocks as $stock) {
            $product = $this->entityManager->getRepository(ProductModel::class)->find($stock->getProductId());
            if ($product) {
                $currentVolume += $product->getVolume() * $stock->getQuantity();
            }
        }
        
        return $currentVolume;
    }
    

    public function getProductByBarcode($barcode)
    {
        try {
            $product = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $barcode]);
            if (!$product) {
                http_response_code(404);
                return ['error' => 'Product not found'];
            }
            return $product->jsonSerialize();
        } catch (\Exception $e) {
            error_log("Exception in getProductByBarcode: " . $e->getMessage());
            throw $e;
        }
    }


    public function getProductByID($id)
    {
        try {
            error_log("Fetching product with ID: " . $id);
            // Vérifiez si l'ID est bien passé
            if (!is_numeric($id)) {
                error_log("Invalid ID: " . $id);
                http_response_code(400);
                return ['error' => 'Invalid ID'];
            }
            
            $product = $this->entityManager->getRepository(ProductModel::class)->find($id);
    
            if (!$product) {
                error_log("Product not found for ID: " . $id);
                http_response_code(404);
                return ['error' => 'Product not found'];
            }
    
            error_log("Product found: " . json_encode($product));
            return $product->jsonSerialize();
        } catch (\Exception $e) {
            error_log("Exception in getProductByID: " . $e->getMessage());
            throw $e;
        }
    }
    
    

    public function updateProduct($barcode, $data)
    {
        try {
            $product = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $barcode]);
            if (!$product) {
                http_response_code(404);
                return ['error' => 'Product not found'];
            }

            if (isset($data['name'])) {
                $product->setName($data['name']);
            }
            if (isset($data['expiration_date'])) {
                $product->setExpirationDate(new \DateTime($data['expiration_date']));
            }
            if (isset($data['volume'])) {
                $product->setVolume($data['volume']);
            }
            $product->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $product->getId(), 'message' => 'Product updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct($barcode)
    {
        try {
            $product = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $barcode]);
            if (!$product) {
                http_response_code(404);
                return ['error' => 'Product not found'];
            }

            $this->entityManager->remove($product);
            $this->entityManager->flush();

            return ['message' => 'Product deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllProducts()
    {
        try {
            $productRepository = $this->entityManager->getRepository(ProductModel::class);
            $products = $productRepository->findAll();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->jsonSerialize();
            }
            return $data;
        } catch (\Exception $e) {
            error_log("Exception in getAllProducts: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProductsInStock()
    {
        try {
            $stockRepository = $this->entityManager->getRepository(StockModel::class);
            $stocks = $stockRepository->findAll();
            
            $productsInStock = [];
            foreach ($stocks as $stock) {
                $product = $this->entityManager->getRepository(ProductModel::class)->find($stock->getProductId());
                if ($product) {
                    $productsInStock[$product->getId()] = [
                        'name' => $product->getName(),
                        'volume' => $stock->getQuantity(),
                        'barcode' => $product->getBarcode()
                    ];
                }
            }

            return $productsInStock;
        } catch (\Exception $e) {
            error_log("Exception in getProductsInStock: " . $e->getMessage());
            throw $e;
        }
    }

}
?>
