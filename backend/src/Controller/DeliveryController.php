<?php
namespace Controller;

use Entity\DeliveryModel;
use Entity\CompanyModel;
use Entity\PlannedRouteModel;
use Entity\VehicleModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
            if (!isset($data['route_name']) || !isset($data['destination']) || !isset($data['recipient_type']) || !isset($data['status']) || !isset($data['warehouse_id']) || !isset($data['email']) || !isset($data['volunteer_name']) || !isset($data['vehicle_id'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new delivery'];
            }
    
            $vehicle = $this->entityManager->find(VehicleModel::class, $data['vehicle_id']);
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }
    
            $today = new \DateTime();
            $existingDelivery = $this->entityManager->getRepository(DeliveryModel::class)->findOneBy([
                'route_name' => $data['route_name'],
                'delivery_date' => $today,
            ]);
    
            if ($existingDelivery) {
                http_response_code(400);
                return ['error' => 'A delivery with the same route_name already exists for today'];
            }
    
            $company = $this->entityManager->getRepository(CompanyModel::class)->findOneBy(['has_stock' => true]);
    
            if (!$company) {
                http_response_code(400);
                return ['error' => 'No companies with stock available'];
            }
    
            $delivery = new DeliveryModel();
            $delivery->setRouteName($data['route_name']);
            $delivery->setDestination($data['destination']);
            $delivery->setRecipientType($data['recipient_type']);
            $delivery->setDeliveryDate($today);
            $delivery->setStatus($data['status']);
            if (isset($data['comment'])) {
                $delivery->setComment($data['comment']);
            }
            $delivery->setWarehouseId($data['warehouse_id']);
            $delivery->setVehicleId($data['vehicle_id']);
            $delivery->setCreatedAt(new \DateTime("now"));
            $delivery->setUpdatedAt(new \DateTime("now"));
    
            $this->entityManager->persist($delivery);
            $this->entityManager->flush();
    
            $plannedRoute = new PlannedRouteModel();
            $plannedRoute->setDelivery($delivery);
            $plannedRoute->setDate($today);
            $this->entityManager->persist($plannedRoute);
            $this->entityManager->flush();
    
            $emailSent = $this->sendEmailNotification($delivery, $data['email'], $data['volunteer_name']);
    
            if (!$emailSent) {
                http_response_code(500);
                return ['error' => 'Delivery created but email notification failed'];
            }
    
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
            if (isset($data['warehouse_id'])) {
                $delivery->setWarehouseId($data['warehouse_id']);
            }
            if (isset($data['vehicle_id'])) {
                $vehicle = $this->entityManager->find(VehicleModel::class, $data['vehicle_id']);
                if (!$vehicle) {
                    http_response_code(404);
                    return ['error' => 'Vehicle not found'];
                }
                $delivery->setVehicleId($data['vehicle_id']);
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

            $pdf = $this->pdfService->createPDF($delivery);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="delivery_' . $id . '.pdf"');
            echo $pdf;
            exit;
        } catch (\Exception $e) {
            error_log("Exception in generateDeliveryPDF: " . $e->getMessage());
            throw $e;
        }
    }

    private function sendEmailNotification($delivery, $email, $volunteerName)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'morewaste1@gmail.com';
            $mail->Password = 'vhpewmlkxxrpnioj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('morewaste1@gmail.com', 'No More Waste');
            $mail->addAddress($email, $volunteerName);

            $pdf = $this->pdfService->createPDF($delivery);

            $mail->addStringAttachment($pdf, 'delivery_details.pdf');

            $mail->isHTML(true);
            $mail->Subject = 'New Delivery Assigned';
            $mail->Body    = $this->generateEmailBody($delivery, $volunteerName);
            $mail->AltBody = $this->generateEmailAltBody($delivery, $volunteerName);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function generateEmailBody($delivery, $volunteerName)
    {
        $googleMapsLink = $this->generateGoogleMapsLink($delivery->getRouteName(), $delivery->getDestination());
        
        return "
            <html>
            <body>
                <h1>New Delivery Assigned</h1>
                <p>Dear {$volunteerName},</p>
                <p>A new delivery has been assigned to you. Please find the details below:</p>
                <ul>
                    <li><strong>Route Name:</strong> {$delivery->getRouteName()}</li>
                    <li><strong>Destination:</strong> {$delivery->getDestination()}</li>
                    <li><strong>Recipient Type:</strong> {$delivery->getRecipientType()}</li>
                    <li><strong>Status:</strong> {$delivery->getStatus()}</li>
                </ul>
                <p>You can view the route on Google Maps <a href=\"{$googleMapsLink}\">here</a>.</p>
                <p>Thank you for your continued support in helping us reduce waste and assist those in need.</p>
                <p>Best regards,</p>
                <p>No More Waste Team</p>
            </body>
            </html>
        ";
    }

    private function generateEmailAltBody($delivery, $volunteerName)
    {
        $googleMapsLink = $this->generateGoogleMapsLink($delivery->getRouteName(), $delivery->getDestination());
        
        return "
            Dear {$volunteerName},\n
            A new delivery has been assigned to you. Please find the details below:\n
            Route Name: {$delivery->getRouteName()}\n
            Destination: {$delivery->getDestination()}\n
            Recipient Type: {$delivery->getRecipientType()}\n
            Status: {$delivery->getStatus()}\n
            \n
            You can view the route on Google Maps here: {$googleMapsLink}\n
            \n
            Thank you for your continued support in helping us reduce waste and assist those in need.\n
            \n
            Best regards,\n
            No More Waste Team
        ";
    }

    private function generateGoogleMapsLink($routeName, $destination)
    {
        return "https://www.google.com/maps/dir/?api=1&origin={$routeName}&destination={$destination}&travelmode=driving";
    }
}
?>
