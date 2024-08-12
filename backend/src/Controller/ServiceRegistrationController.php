<?php
// Path: backend/src/Controller/ServiceRegistrationController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Service\ServiceRegistrationService;

class ServiceRegistrationController
{
    private $entityManager;
    private $serializer;
    private $serviceRegistrationService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->serviceRegistrationService = new ServiceRegistrationService($entityManager);

        // Configurer le normalizer pour le format des dates
        $normalizers = [
            new DateTimeNormalizer(['datetime_format' => 'Y-m-d H:i:s']),
            new ObjectNormalizer()
        ];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createRegistration($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getRegistration((int) $uriParts[1]);
                    } else {
                        return $this->getAllRegistrations();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateRegistration((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Registration ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteRegistration((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Registration ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function createRegistration($data)
    {
        try {
            if (!isset($data['service_id']) || !isset($data['user_id'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for registration'];
            }

            $registration = $this->serviceRegistrationService->createRegistration($data);

            return ['id' => $registration->getId(), 'message' => 'Registration created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createRegistration: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function getRegistration($id)
    {
        try {
            $registration = $this->serviceRegistrationService->getRegistration($id);
            if (!$registration) {
                http_response_code(404);
                return ['error' => 'Registration not found'];
            }
            return json_decode($this->serializer->serialize($registration, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getRegistration: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function updateRegistration($id, $data)
    {
        try {
            if (empty($data)) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $registration = $this->serviceRegistrationService->updateRegistration($id, $data);

            return ['id' => $registration->getId(), 'message' => 'Registration updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateRegistration: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function deleteRegistration($id)
    {
        try {
            $this->serviceRegistrationService->deleteRegistration($id);
            return ['message' => 'Registration deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteRegistration: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function getAllRegistrations()
    {
        try {
            $registrations = $this->serviceRegistrationService->getAllRegistrations();
            return json_decode($this->serializer->serialize($registrations, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllRegistrations: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }
}
?>
