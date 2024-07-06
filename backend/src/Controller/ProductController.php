<?php
// Path: backend/src/Controller/ProductController.php
namespace Controller;

use Entity\ProductModel;
use Entity\StockModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;
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
        // Validate input data
        if (!isset($data['name']) || !isset($data['barcode']) || !isset($data['expiration_date']) || !isset($data['quantity'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for new product'];
        }

        $product = new ProductModel();
        $product->setName($data['name']);
        $product->setBarcode($data['barcode']);
        $product->setExpirationDate(new \DateTime($data['expiration_date']));
        $product->setQuantity($data['quantity']);
        $product->setCreatedAt(new \DateTime("now"));
        $product->setUpdatedAt(new \DateTime("now"));

        // Generate QR code
        $qrCode = QrCode::create($data['barcode'])
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $qrCodeDir = __DIR__ . '/../public/qrcodes';
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

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Add to stocks
        $stock = new StockModel();
        $stock->setProductId($product->getId());
        $stock->setQuantity($data['quantity']);
        $stock->setEntryDate(new \DateTime("now"));
        $stock->setCreatedAt(new \DateTime("now"));
        $stock->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return ['id' => $product->getId(), 'message' => 'Product created successfully'];
    } catch (\Exception $e) {
        error_log("Exception in createProduct: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}

    public function getProductByBarcode($barcode)
    {
        try {
            $product = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $barcode]);
            if (!$product) {
                http_response_code(404);
                return ['error' => 'Product not found'];
            }
            return json_decode($this->serializer->serialize($product, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getProductByBarcode: " . $e->getMessage());
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
            if (isset($data['quantity'])) {
                $product->setQuantity($data['quantity']);
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
            return json_decode($this->serializer->serialize($products, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllProducts: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
