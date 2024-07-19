<?php
namespace Controller;

use Entity\ProductModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ScanController
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

    public function processScan(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $qrCodeData = json_decode($data['qrCodeData'], true);

            // Process the scanned data
            $barcode = $qrCodeData['barcode'];
            $product = $this->entityManager->getRepository(ProductModel::class)->findOneBy(['barcode' => $barcode]);

            if (!$product) {
                return new Response(json_encode(['error' => 'Product not found']), 404);
            }

            // Update product or perform actions as necessary
            // For example, update the quantity, mark it as scanned, etc.
            $product->setScanned(true);
            $this->entityManager->flush();

            return new Response(json_encode(['message' => 'Scan processed successfully', 'product' => $this->serializer->serialize($product, 'json')]), 200);
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => 'An error occurred: ' . $e->getMessage()]), 500);
        }
    }

    public function processRequest($method, $uriParts, $input)
    {
        $request = Request::createFromGlobals();
        return $this->processScan($request);
    }
}
?>
