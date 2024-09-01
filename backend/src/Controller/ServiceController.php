<?php
// Path: backend/src/Controller/ServiceController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Entity\ServiceRegistrationModel;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Entity\ServiceModel;
use Service\ServiceService;

class ServiceController
{
    private $entityManager;
    private $serializer;
    private $serviceService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->serviceService = new ServiceService($entityManager);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createService($input);

                case 'GET':
                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2])) {
                            switch ($uriParts[2]) {
                                case 'capacity':
                                    return $this->getServiceCapacity((int) $uriParts[1]);

                                case 'by_date':
                                    // Extrait le paramètre de la date et le filtre depuis la chaîne de requête
                                    $queryParams = [];
                                    parse_str($_SERVER['QUERY_STRING'], $queryParams);

                                    $dateString = $queryParams['date'] ?? null;
                                    $filter = $queryParams['filter'] ?? 'all';

                                    if ($dateString) {
                                        try {
                                            $date = new \DateTime($dateString);
                                            return $this->getServicesByDate($date, $filter);
                                        } catch (\Exception $e) {
                                            http_response_code(400); // Bad Request
                                            return ['error' => 'Invalid date format'];
                                        }
                                    } else {
                                        http_response_code(400); // Bad Request
                                        return ['error' => 'Date parameter is missing'];
                                    }
                                case 'by_user':
                                    return $this->getServicesByUserId($uriParts[1]);
                                default:
                                    return $this->getService((int) $uriParts[1]);
                            }
                        }
                    } else {
                        return $this->getAllServices();
                    }

                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateService((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Service ID not specified'];

                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteService((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Service ID not specified'];

                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createService($data)
    {
        try {
            if (!isset($data['name']) || !isset($data['description']) || !isset($data['start_schedule']) || !isset($data['end_schedule']) || !isset($data['capacity']) || !isset($data['status']) || !isset($data['location'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new service'];
            }

            $service = $this->serviceService->createService($data);
            return ['id' => $service->getId(), 'message' => 'Service created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createService: " . $e->getMessage());
            throw $e;
        }
    }


    public function getService($id)
    {
        error_log("Attempting to fetch service with ID: $id");

        try {
            $service = $this->serviceService->getService($id);

            if (!$service) {
                error_log("No service found with ID: $id");
                http_response_code(404);
                echo json_encode(['error' => 'Service not found'], JSON_PRETTY_PRINT);
                return;
            }

            return $service->jsonSerialize();

        } catch (\Exception $e) {
            error_log("Exception encountered while fetching service with ID: $id. Exception message: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error'], JSON_PRETTY_PRINT);
            exit();
        }
    }


    public function updateService($id, $data)
    {
        try {
            $service = $this->serviceService->updateService($id, $data);
            return ['id' => $service->getId(), 'message' => 'Service updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateService: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteService($id)
    {
        try {
            $this->serviceService->deleteService($id);
            return ['message' => 'Service deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteService: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllServices()
    {
        try {
            error_log("getAllServices");

            $services = $this->serviceService->getAllServices();
            $serializedServices = [];
            foreach ($services as $service) {
                $serializedServices[] = $service->jsonSerialize();
            }
            return $serializedServices;
        } catch (\Exception $e) {
            error_log("Exception in getAllServices: " . $e->getMessage());
            throw $e;
        }
    }

    public function getServicesByDate(\DateTimeInterface $date, $filter = 'all')
    {
        try {
            error_log("getServicesByDate");
            // Récupère tous les services depuis le service
            $services = $this->serviceService->getAllServices();
            $filteredServices = [];

            // Affiche la date de filtrage reçue
            error_log("Filter Date: " . $date->format('Y-m-d H:i:s'));

            foreach ($services as $service) {
                $serviceDate = $service->getSchedule();

                // Affiche la date de chaque service pour débogage
                error_log("Service ID: " . $service->getId());
                error_log("Service Date: " . ($serviceDate ? $serviceDate->format('Y-m-d H:i:s') : 'null'));

                if (!$serviceDate instanceof \DateTimeInterface) {
                    // Ignore les services dont la date n'est pas valide
                    error_log("Invalid service date, skipping service ID: " . $service->getId());
                    continue;
                }

                switch ($filter) {
                    case 'upcoming':
                        if ($serviceDate > $date) {
                            error_log("Service ID " . $service->getId() . " is upcoming.");
                            $filteredServices[] = $service->jsonSerialize();
                        } else {
                            error_log("Service ID " . $service->getId() . " is not upcoming.");
                        }
                        break;

                    case 'past':
                        if ($serviceDate < $date) {
                            error_log("Service ID " . $service->getId() . " is past.");
                            $filteredServices[] = $service->jsonSerialize();
                        } else {
                            error_log("Service ID " . $service->getId() . " is not past.");
                        }
                        break;

                    case 'to_date':
                        if ($serviceDate->format('Y-m-d') === $date->format('Y-m-d')) {
                            error_log("Service ID " . $service->getId() . " is on the same date.");
                            $filteredServices[] = $service->jsonSerialize();
                        } else {
                            error_log("Service ID " . $service->getId() . " is not on the same date.");
                        }
                        break;

                    case 'all':
                    default:
                        error_log("Service ID " . $service->getId() . " included in all.");
                        $filteredServices[] = $service->jsonSerialize();
                        break;
                }
            }

            // Retourne les services filtrés
            return $filteredServices;
        } catch (\Exception $e) {
            error_log("Exception in getServicesByDate: " . $e->getMessage());
            throw $e;
        }
    }


    public function getServicesByUserId(int $userId): array
    {
        try {
            $qb = $this->entityManager->createQueryBuilder();

            $qb->select('s')
                ->from(ServiceModel::class, 's')
                ->innerJoin(ServiceRegistrationModel::class, 'sr', 'WITH', 's.id = sr.service_id')
                ->where('sr.user_id = :userId')
                ->setParameter('userId', $userId);

            $query = $qb->getQuery();
            $services = $query->getResult();

            return array_map(fn($service) => $service->jsonSerialize(), $services);
        } catch (\Exception $e) {
            error_log("Exception in getServicesByUserId: " . $e->getMessage());
            throw $e;
        }
    }

    public function getServiceCapacity($serviceId)
    {
        try {
            $capacityData = $this->serviceService->getServiceCapacity($serviceId);

            if (!$capacityData) {
                http_response_code(404);
                return ['error' => 'Service capacity data not found'];
            }

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($capacityData, JSON_PRETTY_PRINT);
            exit();

        } catch (\Exception $e) {
            error_log("Exception in getServiceCapacity: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }
}
?>
