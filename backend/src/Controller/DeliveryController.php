<?php
// Path: backend/src/Controller/DeliveryController.php
namespace Controller;

use Entity\DeliveryModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;
use Service\PDFService;

class DeliveryController
{
    private $entityManager;
    private $serializer;
    private $pdfService;

    public function __construct(EntityManager $entityManager, PDFService $pdfService)
    {
        $this->entityManager = $entityManager;
        $this->pdfService = $pdfService;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createDelivery($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2]) && $uriParts[2] === 'pdf') {
                            return $this->generateDeliveryPDF((int) $uriParts[1]);
                        }
                        return $this->getDelivery((int) $uriParts[1]);
                    } else {
                        return $this->getAllDeliveries();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateDelivery((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Delivery ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteDelivery((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Delivery ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createDelivery($data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['route_name']) || !isset($data['destination']) || !isset($data['recipient_type']) || !isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new delivery'];
            }

            $delivery = new DeliveryModel();
            $delivery->setRouteName($data['route_name']);
            $delivery->setDestination($data['destination']);
            $delivery->setRecipientType($data['recipient_type']);
            $delivery->setDeliveryDate(new \DateTime("now"));
            $delivery->setStatus($data['status']);
            if (isset($data['comment'])) {
                $delivery->setComment($data['comment']);
            }
            $delivery->setCreatedAt(new \DateTime("now"));
            $delivery->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($delivery);
            $this->entityManager->flush();

            return ['id' => $delivery->getId(), 'message' => 'Delivery created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createDelivery: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDelivery($id)
    {
        try {
            $delivery = $this->entityManager->find(DeliveryModel::class, $id);
            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }
            return json_decode($this->serializer->serialize($delivery, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getDelivery: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateDelivery($id, $data)
    {
        try {
            $delivery = $this->entityManager->find(DeliveryModel::class, $id);
            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }

            if (isset($data['route_name'])) {
                $delivery->setRouteName($data['route_name']);
            }
            if (isset($data['destination'])) {
                $delivery->setDestination($data['destination']);
            }
            if (isset($data['recipient_type'])) {
                $delivery->setRecipientType($data['recipient_type']);
            }
            if (isset($data['status'])) {
                $delivery->setStatus($data['status']);
            }
            if (isset($data['comment'])) {
                $delivery->setComment($data['comment']);
            }
            $delivery->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $delivery->getId(), 'message' => 'Delivery updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateDelivery: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteDelivery($id)
    {
        try {
            $delivery = $this->entityManager->find(DeliveryModel::class, $id);
            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }

            $this->entityManager->remove($delivery);
            $this->entityManager->flush();

            return ['message' => 'Delivery deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteDelivery: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllDeliveries()
    {
        try {
            $deliveryRepository = $this->entityManager->getRepository(DeliveryModel::class);
            $deliveries = $deliveryRepository->findAll();
            return json_decode($this->serializer->serialize($deliveries, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllDeliveries: " . $e->getMessage());
            throw $e;
        }
    }

    public function generateDeliveryPDF($id)
    {
        try {
            $delivery = $this->entityManager->find(DeliveryModel::class, $id);
            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }

            // Generate PDF
            $pdf = $this->pdfService->createPDF($delivery);

            // Output PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="delivery_' . $id . '.pdf"');
            echo $pdf;
            exit;
        } catch (\Exception $e) {
            error_log("Exception in generateDeliveryPDF: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
