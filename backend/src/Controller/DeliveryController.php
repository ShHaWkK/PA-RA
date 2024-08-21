<?php
namespace Controller;

use Entity\DeliveryModel;
use Entity\RouteModel;
use Entity\DestinationModel;
use Entity\ProductModel;
use Entity\UserModel;
use Entity\VehicleModel;
use Entity\WarehouseModel;
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
                     return $this->createRoute($input);
                case 'PATCH':
                    if (isset($uriParts[1])) {
                        $id = (int) $uriParts[1];

                        if (isset($uriParts[2])) {
                            $operation = $uriParts[2];
                            switch ($operation) {
                                case 'add-delivery':
                                    return $this->addDeliveryToDestination($id, $input);
                                case 'remove-delivery':
                                    return $this->removeDeliveryFromDestination($id);
                                case 'add-destination':
                                    return $this->addDestinationToRoute($id, $input);
                                case 'remove-destination':
                                    return $this->removeDestinationFromRoute($id);
                                default:
                                    http_response_code(400);
                                    return ['error' => 'Invalid operation'];
                            }
                        }
                        http_response_code(400);
                        return ['error' => 'Operation not specified'];
                    }
                    http_response_code(400);
                    return ['error' => 'ID not specified'];
                    break;
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

    public function createRoute(array $data): array
    {
        try {
            // Vérifiez que les champs requis sont présents
            if (!isset($data['name']) || !isset($data['vehicle_id']) || !isset($data['driver_id']) || !isset($data['destinations'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new route'];
            }

            // Récupération de l'entité VehicleModel à partir de l'id fourni
            $vehicle = $this->entityManager->getRepository(VehicleModel::class)
                ->find($data['vehicle_id']);

            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehicle not found'];
            }

            // Récupération de l'entité UserModel à partir de l'id fourni
            $driver = $this->entityManager->getRepository(UserModel::class)
                ->find($data['driver_id']);

            if (!$driver) {
                http_response_code(404);
                return ['error' => 'Driver not found'];
            }

            // Création d'une nouvelle instance de RouteModel
            $route = new RouteModel();
            $route->setName($data['name']);
            $route->setVehicle($vehicle);
            $route->setDriver($driver);
            $route->setStatus('pending'); // Statut par défaut
            $route->setStartTime(new \DateTime()); // Heure de début par défaut
            $route->setCreatedAt(new \DateTime());
            $route->setUpdatedAt(new \DateTime());

            // Traitement des destinations
            foreach ($data['destinations'] as $destinationData) {
                if (!isset($destinationData['address']) || !isset($destinationData['recipient_type']) || !isset($destinationData['deliveries'])) {
                    http_response_code(400);
                    return ['error' => 'Missing required fields for destination'];
                }

                // Création d'une nouvelle instance de DestinationModel
                $destination = new DestinationModel();
                $destination->setAddress($destinationData['address']);
                $destination->setRecipientType($destinationData['recipient_type']);
                $destination->setRoute($route);
                $destination->setStatus('pending'); // Statut par défaut
                $destination->setDeliveryDate(new \DateTime()); // Date de livraison par défaut
                $destination->setCreatedAt(new \DateTime());
                $destination->setUpdatedAt(new \DateTime());

                // Traitement des livraisons
                foreach ($destinationData['deliveries'] as $deliveryData) {
                    if (!isset($deliveryData['product_id']) || !isset($deliveryData['quantity']) || !isset($deliveryData['status'])) {
                        http_response_code(400);
                        return ['error' => 'Missing required fields for delivery'];
                    }

                    // Récupération de l'entité ProductModel à partir de l'id fourni
                    $product = $this->entityManager->getRepository(ProductModel::class)
                        ->find($deliveryData['product_id']);

                    if (!$product) {
                        http_response_code(404);
                        return ['error' => 'Product not found'];
                    }

                    // Création d'une nouvelle instance de DeliveryModel
                    $delivery = new DeliveryModel();
                    $delivery->setDestination($destination);
                    $delivery->setProduct($product);
                    $delivery->setQuantity($deliveryData['quantity']);
                    $delivery->setStatus($deliveryData['status']);
                    $delivery->setCreatedAt(new \DateTime());
                    $delivery->setUpdatedAt(new \DateTime());

                    // Ajout de la livraison à la collection de livraisons de la destination
                    $destination->addDelivery($delivery);

                    // Persistance de la nouvelle livraison en base de données
                    $this->entityManager->persist($delivery);
                }

                // Ajout de la destination à la collection de destinations de la route
                $route->addDestination($destination);

                // Persistance de la nouvelle destination en base de données
                $this->entityManager->persist($destination);
            }

            // Persistance de la nouvelle route en base de données
            $this->entityManager->persist($route);
            $this->entityManager->flush();

            // Retourne la réponse avec les détails de la route créée
            return [
                'id' => $route->getId(),
                'message' => 'Route created successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in createRoute: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while creating the route'];
        }
    }

    public function addDeliveryToDestination(int $destinationId, array $data): array
    {
        try {
            // Vérifiez que les champs requis sont présents
            if (!isset($data['product_id']) || !isset($data['quantity']) || !isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new delivery'];
            }

            // Récupération de l'entité DestinationModel à partir de l'id fourni
            $destination = $this->entityManager->getRepository(DestinationModel::class)
                ->find($destinationId);

            if (!$destination) {
                http_response_code(404);
                return ['error' => 'Destination not found'];
            }

            // Récupération de l'entité ProductModel à partir de l'id fourni
            $product = $this->entityManager->getRepository(ProductModel::class)
                ->find($data['product_id']);

            if (!$product) {
                http_response_code(404);
                return ['error' => 'Product not found'];
            }

            // Création d'une nouvelle instance de DeliveryModel
            $delivery = new DeliveryModel();
            $delivery->setDestination($destination);
            $delivery->setProduct($product);
            $delivery->setQuantity($data['quantity']);
            $delivery->setStatus($data['status']);
            $delivery->setCreatedAt(new \DateTime());
            $delivery->setUpdatedAt(new \DateTime());

            // Ajout de la livraison à la collection de livraisons de la destination
            $destination->addDelivery($delivery);

            // Persistance de la nouvelle livraison en base de données
            $this->entityManager->persist($delivery);
            $this->entityManager->flush();

            // Retourne la réponse avec les détails de la livraison ajoutée
            return [
                'id' => $delivery->getId(),
                'message' => 'Delivery added successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in addDeliveryToDestination: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while adding the delivery'];
        }
    }

    public function removeDeliveryFromDestination(int $deliveryId): array
    {
        try {
            // Récupération de l'entité DeliveryModel à partir de l'id fourni
            $delivery = $this->entityManager->getRepository(DeliveryModel::class)
                ->find($deliveryId);

            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }

            // Récupération de l'entité DestinationModel associée à la livraison
            $destination = $delivery->getDestination();

            // Suppression de la livraison de la collection de livraisons de la destination
            $destination->removeDelivery($delivery);

            // Suppression de la livraison de la base de données
            $this->entityManager->remove($delivery);
            $this->entityManager->flush();

            // Retourne une réponse de succès
            return [
                'message' => 'Delivery removed successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in removeDeliveryFromDestination: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while removing the delivery'];
        }
    }

    public function addDestinationToRoute(int $routeId, array $data): array
    {
        try {
            // Vérifiez que les champs requis sont présents
            if (!isset($data['address']) || !isset($data['recipient_type'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new destination'];
            }

            // Récupération de l'entité RouteModel à partir de l'id fourni
            $route = $this->entityManager->getRepository(RouteModel::class)
                ->find($routeId);

            if (!$route) {
                http_response_code(404);
                return ['error' => 'Route not found'];
            }

            // Création d'une nouvelle instance de DestinationModel
            $destination = new DestinationModel();
            $destination->setAddress($data['address']);
            $destination->setRecipientType($data['recipient_type']);
            $destination->setRoute($route);
            $destination->setStatus('pending'); // Statut par défaut
            $destination->setDeliveryDate(new \DateTime()); // Date de livraison par défaut
            $destination->setCreatedAt(new \DateTime());
            $destination->setUpdatedAt(new \DateTime());

            // Ajout de la destination à la collection de destinations de la route
            $route->addDestination($destination);

            // Persistance de la nouvelle destination en base de données
            $this->entityManager->persist($destination);
            $this->entityManager->flush();

            // Retourne la réponse avec les détails de la destination ajoutée
            return [
                'id' => $destination->getId(),
                'message' => 'Destination added successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in addDestinationToRoute: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while adding the destination'];
        }
    }

    public function removeDestinationFromRoute(int $destinationId): array
    {
        try {
            // Récupération de l'entité DestinationModel à partir de l'id fourni
            $destination = $this->entityManager->getRepository(DestinationModel::class)
                ->find($destinationId);

            if (!$destination) {
                http_response_code(404);
                return ['error' => 'Destination not found'];
            }

            // Récupération de l'entité RouteModel associée à la destination
            $route = $destination->getRoute();

            // Suppression de la destination de la collection de destinations de la route
            $route->removeDestination($destination);

            // Suppression de la destination de la base de données
            $this->entityManager->remove($destination);
            $this->entityManager->flush();

            // Retourne une réponse de succès
            return [
                'message' => 'Destination removed successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in removeDestinationFromRoute: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while removing the destination'];
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

            if (isset($data['route_id'])) {
                $route = $this->entityManager->find(RouteModel::class, $data['route_id']);
                if (!$route) {
                    http_response_code(404);
                    return ['error' => 'Route not found'];
                }
                $delivery->setRoute($route);
            }
            if (isset($data['status'])) {
                $delivery->setStatus($data['status']);
            }
            if (isset($data['warehouse_id'])) {
                $warehouse = $this->entityManager->find(WarehouseModel::class, $data['warehouse_id']);
                if (!$warehouse) {
                    http_response_code(404);
                    return ['error' => 'Warehouse not found'];
                }
                $delivery->setWarehouse($warehouse);
            }
            if (isset($data['vehicle_id'])) {
                $vehicle = $this->entityManager->find(VehicleModel::class, $data['vehicle_id']);
                if (!$vehicle) {
                    http_response_code(404);
                    return ['error' => 'Vehicle not found'];
                }
                $delivery->setVehicle($vehicle);
            }
            if (isset($data['comment'])) {
                $delivery->setComment($data['comment']);
            }
            $delivery->setUpdatedAt(new \DateTime("now"));

            // Gestion des destinations
            if (isset($data['destinations'])) {
                foreach ($delivery->getDestinations() as $destination) {
                    $this->entityManager->remove($destination);
                }
                foreach ($data['destinations'] as $destinationData) {
                    $destination = new DestinationModel();
                    $destination->setAddress($destinationData['address']);
                    $destination->setRecipientType($destinationData['recipient_type']);
                    $destination->setDelivery($delivery);
                    $this->entityManager->persist($destination);
                }
            }

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

            foreach ($delivery->getDestinations() as $destination) {
                $this->entityManager->remove($destination);
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
            // Récupérer toutes les livraisons depuis le repository
            $deliveries = $this->entityManager->getRepository(DeliveryModel::class)->findAll();

            // Utiliser jsonSerialize pour chaque livraison
            $serializedDeliveries = [];
            foreach ($deliveries as $delivery) {
                $serializedDeliveries[] = $delivery->jsonSerialize();
            }

            return $serializedDeliveries;
        } catch (\Exception $e) {
            // Log de l'exception
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
        $googleMapsLink = $this->generateGoogleMapsLink($delivery->getRoute()->getName(), $delivery->getDestinations());

        return "
            <html>
            <body>
                <h1>New Delivery Assigned</h1>
                <p>Dear {$volunteerName},</p>
                <p>A new delivery has been assigned to you. Please find the details below:</p>
                <ul>
                    <li><strong>Route Name:</strong> {$delivery->getRoute()->getName()}</li>
                    <li><strong>Status:</strong> {$delivery->getStatus()}</li>
                    <li><strong>Destinations:</strong> " . implode(", ", array_map(function($dest) { return $dest->getAddress(); }, $delivery->getDestinations()->toArray())) . "</li>
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
        $googleMapsLink = $this->generateGoogleMapsLink($delivery->getRoute()->getName(), $delivery->getDestinations());

        return "
            Dear {$volunteerName},\n
            A new delivery has been assigned to you. Please find the details below:\n
            Route Name: {$delivery->getRoute()->getName()}\n
            Status: {$delivery->getStatus()}\n
            Destinations: " . implode(", ", array_map(function($dest) { return $dest->getAddress(); }, $delivery->getDestinations()->toArray())) . "\n
            \n
            You can view the route on Google Maps here: {$googleMapsLink}\n
            \n
            Thank you for your continued support in helping us reduce waste and assist those in need.\n
            \n
            Best regards,\n
            No More Waste Team
        ";
    }

    private function generateGoogleMapsLink($routeName, $destinations)
    {
        $destinationAddresses = array_map(function($dest) { return urlencode($dest->getAddress()); }, $destinations->toArray());
        $destinationParams = implode("&waypoints=", $destinationAddresses);

        return "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($routeName) . "&destination=" . end($destinationAddresses) . "&waypoints={$destinationParams}&travelmode=driving";
    }
}
?>