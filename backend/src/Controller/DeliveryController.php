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
    private $excelService;
    private $emailService;
    private $googleMapsService;


    public function __construct(EntityManager $entityManager, PDFService $pdfService,$excelService, $emailService, $googleMapsService)
    {
        $this->entityManager = $entityManager;
        $this->excelService = $excelService;
        $this->emailService = $emailService;
        $this->pdfService = $pdfService;
        $this->googleMapsService = $googleMapsService;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    if (isset($uriParts[1])) {
                        switch ($uriParts[1]) {
                            case 'generateExcel':
                                if (isset($uriParts[2])) {
                                    return $this->exportRouteToExcel($uriParts[2]);
                                } else {
                                    http_response_code(400);
                                    return ['error' => 'ID not specified'];
                                }
                            case 'sendExcelByMail':
                                if (isset($uriParts[2])) {
                                    return $this->sendRouteExcelEmail($uriParts[2],$uriParts[3]);
                                } else {
                                    http_response_code(400);
                                    return ['error' => 'ID not specified'];
                                }
                            case 'generatePDF':
                                if (isset($uriParts[2])) {
                                    return $this->generateDeliveryPDF($uriParts[2]);
                                } else {
                                    http_response_code(400);
                                    return ['error' => 'ID not specified'];
                                }
                            default:
//                                return $this->testGoogleMaps();
                                return $this->createRoute($input);
                        }
                    } else {
                        http_response_code(400);
                        return ['error' => 'Invalid URI'];
                    }

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

                case 'GET':
                    if (isset($uriParts[1])) {
                        $id = (int) $uriParts[1];

                        if (isset($uriParts[2]) && $uriParts[2] === 'route') {
                            return $this->getRouteById($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'delivery') {
                            return $this->getDeliveryById($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'destination') {
                            return $this->getDestinationById($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'route-destinations') {
                            return $this->getDestinationsByRoute($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'destination-deliveries') {
                            return $this->getDeliveriesByDestination($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'get-excel') {
                            return $this->getRouteExcel($id);
                        }

                        return $this->getRouteById($id);
                    } else {
                        return $this->getAllRoutes($_GET);
                    }

                case 'PUT':
                    if (isset($uriParts[1])) {
                        $id = (int) $uriParts[1];

                        if (isset($uriParts[2]) && $uriParts[2] === 'route') {
                            $route = $this->entityManager->getRepository(RouteModel::class)->find($id);
                            if ($route) {
                                return $this->updateEntityFields($route, $input);
                            } else {
                                http_response_code(404);
                                return ['error' => 'Route not found'];
                            }
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'delivery') {
                            $delivery = $this->entityManager->getRepository(DeliveryModel::class)->find($id);
                            if ($delivery) {
                                return $this->updateEntityFields($delivery, $input);
                            } else {
                                http_response_code(404);
                                return ['error' => 'Delivery not found'];
                            }
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'destination') {
                            $destination = $this->entityManager->getRepository(DestinationModel::class)->find($id);
                            if ($destination) {
                                return $this->updateEntityFields($destination, $input);
                            } else {
                                http_response_code(404);
                                return ['error' => 'Destination not found'];
                            }
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'destination-deliveries') {
                            $destination = $this->entityManager->getRepository(DestinationModel::class)->find($id);
                            if ($destination) {
                                return $this->updateDestinationAndDeliveries($id,$input);
                            } else {
                                http_response_code(404);
                                return ['error' => 'Destination not found'];
                            }
                        }

                        http_response_code(400);
                        return ['error' => 'Invalid operation'];
                    }
                    http_response_code(400);
                    return ['error' => 'ID not specified'];

                case 'DELETE':
                    if (isset($uriParts[1])) {
                        $id = (int) $uriParts[1];

                        if (isset($uriParts[2]) && $uriParts[2] === 'route') {
                            return $this->deleteRoute($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'delivery') {
                            return $this->removeDeliveryFromDestination($id);
                        }

                        if (isset($uriParts[2]) && $uriParts[2] === 'destination') {
                            return $this->removeDestinationFromRoute($id);
                        }

                        http_response_code(400);
                        return ['error' => 'Invalid operation'];
                    }
                    http_response_code(400);
                    return ['error' => 'ID not specified'];

                default:
                    http_response_code(405);
                    return ['error' => 'Method not allowed'];
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
            $route->setStartTime(new \DateTime($data['date']));
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

            $this->exportRouteToExcel($route->getId());
            $this->generateDeliveryPDF($route->getId());

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
            if (!isset($data['address']) || !isset($data['recipient_type']) || !isset($data['warehouse_id']) || !isset($data['deliveries'])) {
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

            // Récupération de l'entité WarehouseModel à partir de l'id fourni
            $warehouse = $this->entityManager->getRepository(WarehouseModel::class)
                ->find($data['warehouse_id']);

            if (!$warehouse) {
                http_response_code(404);
                return ['error' => 'Warehouse not found'];
            }

            // Création d'une nouvelle instance de DestinationModel
            $destination = new DestinationModel();
            $destination->setAddress($data['address']);
            $destination->setRecipientType($data['recipient_type']);
            $destination->setWarehouse($warehouse);
            $destination->setRoute($route);
            $destination->setStatus('pending'); // Statut par défaut
            $destination->setDeliveryDate(isset($data['delivery_date']) ? new \DateTime($data['delivery_date']) : new \DateTime()); // Date de livraison par défaut
            $destination->setCreatedAt(new \DateTime());
            $destination->setUpdatedAt(new \DateTime());

            // Ajout du commentaire s'il est présent dans les données
            if (isset($data['comment'])) {
                $destination->setComment($data['comment']);
            }

            // Traitement des livraisons associées à cette destination
            foreach ($data['deliveries'] as $deliveryData) {
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
            $this->entityManager->flush();

            // Retourne la réponse avec les détails de la destination ajoutée
            return [
                'id' => $destination->getId(),
                'message' => 'Destination and associated deliveries added successfully',
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in addDestinationToRoute: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while adding the destination and deliveries'];
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

    public function getAllRoutes(array $queryParameters): array
    {
        try {
            $queryBuilder = $this->entityManager->getRepository(RouteModel::class)->createQueryBuilder('r');

            // Filtrer par date de début (start_date)
            if (isset($queryParameters['start_date'])) {
                $startDate = \DateTime::createFromFormat('Y-m-d', $queryParameters['start_date']);
                if ($startDate) {
                    $endStartDate = clone $startDate;
                    $endStartDate->modify('+1 day'); // Fin de la journée
                    $queryBuilder->andWhere('r.start_time >= :start_date')
                        ->andWhere('r.start_time < :end_start_date')
                        ->setParameter('start_date', $startDate->format('Y-m-d'))
                        ->setParameter('end_start_date', $endStartDate->format('Y-m-d'));
                } else {
                    error_log("Invalid start date format: " . $queryParameters['start_date']);
                }
            }

            // Filtrer par date de fin (end_date)
            if (isset($queryParameters['end_date'])) {
                $endDate = \DateTime::createFromFormat('Y-m-d', $queryParameters['end_date']);
                if ($endDate) {
                    $endEndDate = clone $endDate;
                    $endEndDate->modify('+1 day'); // Fin de la journée
                    $queryBuilder->andWhere('r.start_time >= :start_date')
                        ->andWhere('r.start_time < :end_end_date')
                        ->setParameter('start_date', $endDate->format('Y-m-d'))
                        ->setParameter('end_end_date', $endEndDate->format('Y-m-d'));
                } else {
                    error_log("Invalid end date format: " . $queryParameters['end_date']);
                }
            }

            // Autres critères de recherche
            if (isset($queryParameters['vehicle_id'])) {
                $queryBuilder->andWhere('r.vehicle = :vehicle_id')
                    ->setParameter('vehicle_id', $queryParameters['vehicle_id']);
            }

            if (isset($queryParameters['driver_id'])) {
                $queryBuilder->andWhere('r.driver = :driver_id')
                    ->setParameter('driver_id', $queryParameters['driver_id']);
            }

            if (isset($queryParameters['status'])) {
                $queryBuilder->andWhere('r.status = :status')
                    ->setParameter('status', $queryParameters['status']);
            }

            // Exécution de la requête
            $routes = $queryBuilder->getQuery()->getResult();

            // Vérification si des routes existent
            if (empty($routes)) {
                http_response_code(404);
                return ['error' => 'No routes found with the given criteria'];
            }

            // Transforme chaque route en tableau associatif grâce à jsonSerialize
            $routesArray = array_map(function($route) {
                return $route->jsonSerialize();
            }, $routes);

            // Retourne les routes au format JSON
            return [
                'routes' => $routesArray,
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in getAllRoutes: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while retrieving the routes'];
        }
    }

    private function getDestinationsByRoute(int $routeId)
    {
        // Récupérer l'objet RouteModel à partir de l'ID
        $route = $this->entityManager->getRepository(RouteModel::class)->find($routeId);

        // Vérifier si la route existe
        if (!$route) {
            http_response_code(404);
            return ['message' => "Route with ID $routeId not found"];
        }

        // Récupérer les destinations associées à cette route
        $destinations = $route->getDestinations();

        // Vérifier si les destinations existent
        if ($destinations->isEmpty()) {
            http_response_code(404);
            return ['error' => 'No destinations found for this route'];
        }

        // Sérialiser les destinations
        $serializedDestinations = [];
        foreach ($destinations as $destination) {
            $serializedDestinations[] = $destination->jsonSerialize();
        }

        return $serializedDestinations;
    }

    public function getDeliveriesByDestination(int $destinationId): array
    {
        try {
            // Récupérer l'objet DestinationModel à partir de l'ID
            $destination = $this->entityManager->getRepository(DestinationModel::class)->find($destinationId);

            // Vérifier si la destination existe
            if (!$destination) {
                http_response_code(404);
                return ['message' => "Destination with ID $destinationId not found"];
            }

            // Récupérer les livraisons associées à cette destination
            $deliveries = $destination->getDeliveries();

            // Vérifier si les livraisons existent
            if ($deliveries->isEmpty()) {
                http_response_code(404);
                return ['error' => 'No deliveries found for this destination'];
            }

            // Sérialiser les livraisons
            $serializedDeliveries = [];
            foreach ($deliveries as $delivery) {
                $serializedDeliveries[] = $delivery->jsonSerialize();
            }

            return $serializedDeliveries;

        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in getDeliveriesByDestination: " . $e->getMessage());

            // Retourner une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while retrieving deliveries'];
        }
    }

    public function getRouteById(int $id): array
    {
        try {
            // Récupération de l'entité RouteModel à partir de l'id fourni
            $route = $this->entityManager->getRepository(RouteModel::class)->find($id);

            // Vérification si la route existe
            if (!$route) {
                http_response_code(404);
                return ['error' => 'Route not found'];
            }

            // Retourne les détails de la route en format JSON
            return [
                'route' => $route->jsonSerialize(),
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in getRouteById: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while retrieving the route'];
        }
    }

    public function getDestinationById(int $id): array
    {
        try {
            // Récupération de l'entité DestinationModel à partir de l'id fourni
            $destination = $this->entityManager->getRepository(DestinationModel::class)->find($id);

            // Vérification si la destination existe
            if (!$destination) {
                http_response_code(404);
                return ['error' => 'Destination not found'];
            }

            // Retourne les détails de la destination en format JSON
            return [
                'destination' => $destination->jsonSerialize(),
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in getDestinationById: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while retrieving the destination'];
        }
    }

    public function getDeliveryById(int $id): array
    {
        try {
            // Récupération de l'entité DeliveryModel à partir de l'id fourni
            $delivery = $this->entityManager->getRepository(DeliveryModel::class)->find($id);

            // Vérification si la livraison existe
            if (!$delivery) {
                http_response_code(404);
                return ['error' => 'Delivery not found'];
            }

            // Retourne les détails de la livraison en format JSON
            return [
                'delivery' => $delivery->jsonSerialize(),
            ];
        } catch (\Exception $e) {
            // Log de l'exception pour débogage
            error_log("Exception in getDeliveryById: " . $e->getMessage());

            // Retourne une réponse d'erreur avec le message de l'exception
            http_response_code(500);
            return ['error' => 'An error occurred while retrieving the delivery'];
        }
    }

    public function updateEntityFields(object $entity, array $data): array
    {
        try {
            // Vérification des champs de la route
            if ($entity instanceof RouteModel) {
                if (isset($data['name'])) {
                    $entity->setName($data['name']);
                }
                if (isset($data['vehicle_id'])) {
                    $vehicle = $this->entityManager->getRepository(VehicleModel::class)->find($data['vehicle_id']);
                    if ($vehicle) {
                        $entity->setVehicle($vehicle);
                    } else {
                        http_response_code(404);
                        return ['error' => 'Vehicle not found'];
                    }
                }
                if (isset($data['driver_id'])) {
                    $driver = $this->entityManager->getRepository(UserModel::class)->find($data['driver_id']);
                    if ($driver) {
                        $entity->setDriver($driver);
                    } else {
                        http_response_code(404);
                        return ['error' => 'Driver not found'];
                    }
                }
                if (isset($data['start_time'])) {
                    $entity->setStartTime(new \DateTime($data['start_time']));
                }
                if (isset($data['end_time'])) {
                    $entity->setEndTime(new \DateTime($data['end_time']));
                }
                if (isset($data['status'])) {
                    $entity->setStatus($data['status']);
                }

            }

            // Vérification des champs de la livraison
            if ($entity instanceof DeliveryModel) {
                if (isset($data['product_id'])) {
                    $product = $this->entityManager->getRepository(ProductModel::class)->find($data['product_id']);
                    if ($product) {
                        $entity->setProduct($product);
                    } else {
                        http_response_code(404);
                        return ['error' => 'Product not found'];
                    }
                }
                if (isset($data['quantity'])) {
                    $entity->setQuantity($data['quantity']);
                }
                if (isset($data['status'])) {
                    $entity->setStatus($data['status']);
                }
                if (isset($data['comment'])) {
                    $entity->setComment($data['comment']);
                }
            }

            // Vérification des champs de la destination
            if ($entity instanceof DestinationModel) {
                if (isset($data['address'])) {
                    $entity->setAddress($data['address']);
                }
                if (isset($data['recipient_type'])) {
                    $entity->setRecipientType($data['recipient_type']);
                }
                if (isset($data['delivery_date'])) {
                    $entity->setDeliveryDate(new \DateTime($data['delivery_date']));
                }
                if (isset($data['status'])) {
                    $entity->setStatus($data['status']);
                }
                if (isset($data['comment'])) {
                    $entity->setComment($data['comment']);
                }
                if (isset($data['warehouse_id'])) {
                    $warehouse = $this->entityManager->getRepository(WarehouseModel::class)->find($data['warehouse_id']);
                    if ($warehouse) {
                        $entity->setWarehouse($warehouse);
                    } else {
                        http_response_code(404);
                        return ['error' => 'Warehouse not found'];
                    }
                }
            }

            // Mise à jour des timestamps
            $entity->setUpdatedAt(new \DateTime());

            // Persister les changements dans la base de données
            $this->entityManager->flush();

            $this->exportRouteToExcel($entity->getId());
            $this->generateDeliveryPDF($entity->getId());

            return [
                'id' => $entity->getId(),
                'message' => 'Entity updated successfully',
            ];
        } catch (\Exception $e) {
            error_log("Exception in updateEntityFields: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'An error occurred while updating the entity'];
        }
    }

    public function updateDestinationAndDeliveries(int $destinationId, array $data): array
    {
        try {
            // Récupération de l'entité DestinationModel à partir de l'id fourni
            $destination = $this->entityManager->getRepository(DestinationModel::class)->find($destinationId);

            if (!$destination) {
                http_response_code(404);
                return ['error' => 'Destination not found'];
            }

            // Mise à jour des champs de la destination
            if (isset($data['address'])) {
                $destination->setAddress($data['address']);
            }
            if (isset($data['recipient_type'])) {
                $destination->setRecipientType($data['recipient_type']);
            }
            if (isset($data['delivery_date'])) {
                $destination->setDeliveryDate(new \DateTime($data['delivery_date']));
            }
            if (isset($data['status'])) {
                $destination->setStatus($data['status']);
            }
            if (isset($data['comment'])) {
                $destination->setComment($data['comment']);
            }
            if (isset($data['warehouse_id'])) {
                $warehouse = $this->entityManager->getRepository(WarehouseModel::class)->find($data['warehouse_id']);
                if ($warehouse) {
                    $destination->setWarehouse($warehouse);
                } else {
                    http_response_code(404);
                    return ['error' => 'Warehouse not found'];
                }
            }

            // Mise à jour des livraisons associées
            if (isset($data['deliveries']) && is_array($data['deliveries'])) {
                foreach ($data['deliveries'] as $deliveryData) {
                    if (isset($deliveryData['id'])) {
                        // Récupération de la livraison à mettre à jour
                        $delivery = $this->entityManager->getRepository(DeliveryModel::class)->find($deliveryData['id']);

                        if ($delivery && $delivery->getDestination() === $destination) {
                            // Mise à jour des champs de la livraison
                            if (isset($deliveryData['product_id'])) {
                                $product = $this->entityManager->getRepository(ProductModel::class)->find($deliveryData['product_id']);
                                if ($product) {
                                    $delivery->setProduct($product);
                                } else {
                                    http_response_code(404);
                                    return ['error' => 'Product not found'];
                                }
                            }
                            if (isset($deliveryData['quantity'])) {
                                $delivery->setQuantity($deliveryData['quantity']);
                            }
                            if (isset($deliveryData['status'])) {
                                $delivery->setStatus($deliveryData['status']);
                            }
                            if (isset($deliveryData['comment'])) {
                                $delivery->setComment($deliveryData['comment']);
                            }
                        }
                    } else {
                        // Création d'une nouvelle livraison si l'ID n'est pas fourni
                        $delivery = new DeliveryModel();
                        $delivery->setDestination($destination);

                        if (isset($deliveryData['product_id'])) {
                            $product = $this->entityManager->getRepository(ProductModel::class)->find($deliveryData['product_id']);
                            if ($product) {
                                $delivery->setProduct($product);
                            } else {
                                http_response_code(404);
                                return ['error' => 'Product not found'];
                            }
                        }
                        if (isset($deliveryData['quantity'])) {
                            $delivery->setQuantity($deliveryData['quantity']);
                        }
                        if (isset($deliveryData['status'])) {
                            $delivery->setStatus($deliveryData['status']);
                        }
                        if (isset($deliveryData['comment'])) {
                            $delivery->setComment($deliveryData['comment']);
                        }

                        $this->entityManager->persist($delivery);
                    }
                }
            }

            // Mise à jour des timestamps
            $destination->setUpdatedAt(new \DateTime());

            // Persister les changements dans la base de données
            $this->entityManager->flush();

            return [
                'id' => $destination->getId(),
                'message' => 'Destination and deliveries updated successfully',
            ];
        } catch (\Exception $e) {
            error_log("Exception in updateDestinationAndDeliveries: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'An error occurred while updating the destination and deliveries'];
        }
    }

    public function deleteRoute(int $id): array
    {
        try {
            // Trouver la route par son ID
            $route = $this->entityManager->getRepository(RouteModel::class)->find($id);

            if (!$route) {
                http_response_code(404);
                return ['error' => 'Route not found'];
            }

            // Supprimer la route
            $this->entityManager->remove($route);
            $this->entityManager->flush();

            return [
                'message' => 'Route deleted successfully',
            ];
        } catch (\Exception $e) {
            error_log("Exception in deleteRoute: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'An error occurred while deleting the route'];
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

    private function getRouteData(int $routeId): array
    {
        // Récupérer la route
        $route = $this->entityManager->find(RouteModel::class, $routeId);

        if (!$route) {
            http_response_code(404);
            throw new \Exception("Route with ID $routeId not found.");
        }

        // Structurer les données de la route
        $routeData = [
            'name' => $route->getName(),
            'vehicle' => $route->getVehicle()->getLicensePlate(),
            'driver' => $route->getDriver()->getFirstName() . ' ' . $route->getDriver()->getLastName(),
            'start_time' => $route->getStartTime(),
            'end_time' => $route->getEndTime(),
            'status' => $route->getStatus(),
            'destinations' => []
        ];

        // Récupérer les destinations associées à la route
        $destinations = $this->entityManager->getRepository(DestinationModel::class)
            ->findBy(['route' => $route]);

        foreach ($destinations as $destination) {
            $destinationData = [
                'address' => $destination->getAddress(),
                'recipient_type' => $destination->getRecipientType(),
                'delivery_date' => $destination->getDeliveryDate(),
                'status' => $destination->getStatus(),
                'deliveries' => []
            ];

            // Récupérer les livraisons associées à la destination
            $deliveries = $this->entityManager->getRepository(DeliveryModel::class)
                ->findBy(['destination' => $destination]);

            foreach ($deliveries as $delivery) {
                $deliveryData = [
                    'product' => $delivery->getProduct()->getName(),
                    'quantity' => $delivery->getQuantity(),
                    'status' => $delivery->getStatus(),
                    'comment' => $delivery->getComment()
                ];

                $destinationData['deliveries'][] = $deliveryData;
            }

            $routeData['destinations'][] = $destinationData;
        }

        return $routeData;
    }

    public function exportRouteToExcel(int $routeId): array
    {
        try {
            // Récupérer les données structurées de la route
            $routeData = $this->getRouteData($routeId);

            // Générer le fichier Excel et récupérer le chemin du fichier
            $excelFilePath = $this->excelService->generateDeliveryRouteExcel($routeData);

            // Mettre à jour le modèle de route avec le chemin du fichier Excel
            $route = $this->entityManager->find(RouteModel::class, $routeId);
            $route->setExcelPath($excelFilePath);
            $this->entityManager->flush();

            // Optionnel : Envoyer le fichier par email
            $recipient_email = $route->getDriver()->getEmail();
            $this->sendRouteExcelEmail($routeId, $recipient_email);

            return ['message' => 'Excel file successfully generated.'];

        } catch (\Exception $e) {
            error_log("Exception dans exportRouteToExcel: " . $e->getMessage());
            return [
                'message' => 'Une erreur est survenue lors de la génération du fichier Excel.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getRouteExcel(int $routeId): array
    {
        try {
            // Récupérer la route
            $route = $this->entityManager->find(RouteModel::class, $routeId);

            if (!$route) {
                http_response_code(404);
                return ["Route with ID $routeId not found."];
            }

            // Récupérer le chemin relatif du fichier Excel
            $relativeFilePath = $route->getExcelPath();

            if (!$relativeFilePath) {
                http_response_code(400);
                return ["Invalid file path. No file path associated with route ID $routeId."];
            }

            // Obtenir le contenu du fichier et d'autres informations depuis le service
            $fileData = $this->excelService->getFileContent($relativeFilePath);

            if (isset($fileData['error'])) {
                http_response_code(404);
                return [$fileData['error']];
            }

            // Définir les headers pour le téléchargement
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileData['filename'] . '"');
            header('Content-Length: ' . $fileData['size']);

            // Envoyer le contenu du fichier
            echo $fileData['content'];
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            return ["An error occurred while retrieving the Excel file.", 'error' => $e->getMessage()];
        }
    }

    public function sendRouteExcelEmail($routeId, $recipientEmail)
    {
        try {
            // Récupérer la collection à partir de l'ID
            $route = $this->entityManager->find(RouteModel::class, $routeId);
            if (!$route) {
                http_response_code(404);
                throw new \Exception("Collection with ID $routeId not found.");
            }

            if($recipientEmail === null){
                $recipientEmail = $route->getDriver()->getEmail();
            }

            $relativeFilePath = $route->getExcelPath();

            $fileData = $this->excelService->getFileContent($relativeFilePath);

            // Vérifier si le contenu du fichier est disponible
            if (!isset($fileData['content']) || empty($fileData['content'])) {
                http_response_code(404);
                throw new \Exception("Excel file content not found or empty at path: $relativeFilePath.");
            }

            $routeDate = $route->getStartTime();
            if (!$routeDate) {
                http_response_code(404);
                throw new \Exception("Delivery date is not set.");
            }
            $formattedDate = $routeDate->format('d-m-Y');

            $subject = "Votre fichier Excel de livraison du {$formattedDate}";
            $body = "Ci-joint votre fichier excel de livraison du {$formattedDate}.";

            $fileName = basename($relativeFilePath);

            $result = $this->emailService->sendExcelFile($recipientEmail, $subject, $body, $fileName, $fileData['content']);

            if (!$result) {
                http_response_code(400);
                throw new \Exception("Failed to send email to $recipientEmail.");
            }

            return ['message' => 'Mail successfully sent'];
        } catch (\Exception $e) {
            error_log("Exception in sendCollectionExcelEmail: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function generateDeliveryPDF(int $routeId)
    {
        try {
            // Récupérer les données de la route
            $route = $this->entityManager->find(RouteModel::class, $routeId);
            if (!$route) {
                http_response_code(404);
                return ['error' => 'Route not found'];
            }

            // Générer le PDF
            $pdfContent = $this->pdfService->createPDF($route);

            // Définir le chemin du fichier PDF
            $pdfFilePath = sys_get_temp_dir() . '/delivery_' . $routeId . '.pdf';
            file_put_contents($pdfFilePath, $pdfContent);

            // Mettre à jour le modèle de route avec le chemin du fichier PDF
            $route->setPdfPath($pdfFilePath);
            $this->entityManager->flush();

            // Récupérer l'email du conducteur associé à la route
            $driver = $route->getDriver();
            if ($driver) {
                $recipientEmail = $driver->getEmail();
            } else {
                throw new \Exception('Driver not found for the route');
            }

            // Envoi du fichier PDF par email
            $this->emailService->sendRoutePDFEmail($pdfFilePath, $recipientEmail);

        } catch (\Exception $e) {
            error_log("Exception in generateDeliveryPDF: " . $e->getMessage());
            throw $e;
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

    private function testGoogleMaps()
    {
        $addresses = [
            "10 Rue de Rivoli, 75001 Paris, France",
            "1 Avenue des Champs-Élysées, 75008 Paris, France",
            "3 Rue de la Paix, 75002 Paris, France",
            "5 Boulevard Montmartre, 75002 Paris, France",
            "15 Rue de la République, 69001 Lyon, France",
            "25 Rue de la Liberté, 69003 Lyon, France",
            "50 Rue du Faubourg Saint-Antoine, 75011 Paris, France",
            "12 Rue de la Gare, 69007 Lyon, France",
            "100 Rue de la République, 13002 Marseille, France",
            "20 Place de la Bourse, 33000 Bordeaux, France"
        ];

        $host = getenv('MYSQL_HOST');
        error_log(print_r("host",true));
        error_log(print_r($host,true));

        $apiKey = getenv('GOOGLE_MAPS_API_KEY');
        error_log(print_r("apikey",true));
        error_log(print_r($apiKey,true));

        $optimizedRoute = $this->googleMapsService->getOptimizedRoute($addresses, $apiKey);

        // Traiter et afficher $optimizedRoute selon vos besoins
        print_r($optimizedRoute);
    }

}
?>