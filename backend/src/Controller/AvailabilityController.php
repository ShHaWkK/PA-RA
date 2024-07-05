<?php
// Path: backend/src/Controller/AvailabilityController.php
namespace Controller;

use Entity\AvailabilityModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;

class AvailabilityController
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
                    return $this->createAvailability($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getAvailability((int) $uriParts[1]);
                    } else {
                        return $this->getAllAvailabilities();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateAvailability((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Availability ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteAvailability((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Availability ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createAvailability($data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['user_id']) || !isset($data['day_of_week']) || !isset($data['start_time']) || !isset($data['end_time'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new availability'];
            }

            $availability = new AvailabilityModel();
            $availability->setUserId($data['user_id']);
            $availability->setDayOfWeek($data['day_of_week']);
            $availability->setStartTime(new \DateTime($data['start_time']));
            $availability->setEndTime(new \DateTime($data['end_time']));
            $availability->setCreatedAt(new \DateTime("now"));
            $availability->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($availability);
            $this->entityManager->flush();

            return ['id' => $availability->getId(), 'message' => 'Availability created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailability($id)
    {
        try {
            $availability = $this->entityManager->find(AvailabilityModel::class, $id);
            if (!$availability) {
                http_response_code(404);
                return ['error' => 'Availability not found'];
            }
            return json_decode($this->serializer->serialize($availability, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateAvailability($id, $data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['day_of_week']) && !isset($data['start_time']) && !isset($data['end_time'])) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $availability = $this->entityManager->find(AvailabilityModel::class, $id);
            if (!$availability) {
                http_response_code(404);
                return ['error' => 'Availability not found'];
            }

            if (isset($data['day_of_week'])) {
                $availability->setDayOfWeek($data['day_of_week']);
            }
            if (isset($data['start_time'])) {
                $availability->setStartTime(new \DateTime($data['start_time']));
            }
            if (isset($data['end_time'])) {
                $availability->setEndTime(new \DateTime($data['end_time']));
            }
            $availability->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $availability->getId(), 'message' => 'Availability updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteAvailability($id)
    {
        try {
            $availability = $this->entityManager->find(AvailabilityModel::class, $id);
            if (!$availability) {
                http_response_code(404);
                return ['error' => 'Availability not found'];
            }

            $this->entityManager->remove($availability);
            $this->entityManager->flush();

            return ['message' => 'Availability deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllAvailabilities()
    {
        try {
            $availabilityRepository = $this->entityManager->getRepository(AvailabilityModel::class);
            $availabilities = $availabilityRepository->findAll();
            return json_decode($this->serializer->serialize($availabilities, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllAvailabilities: " . $e->getMessage());
            throw $e;
        }
    }
}
