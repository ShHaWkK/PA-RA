<?php
// Path: backend/src/Controller/ServiceScheduleController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Service\ServiceService;

class ServiceScheduleController
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
                    return $this->createServiceSchedule($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getServiceSchedule((int) $uriParts[1]);
                    } else {
                        return $this->getAllServiceSchedules();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateServiceSchedule((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Service Schedule ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteServiceSchedule((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Service Schedule ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createServiceSchedule($data)
    {
        try {
            $schedule = $this->serviceService->createServiceSchedule($data);
            return ['id' => $schedule->getId(), 'message' => 'Service Schedule created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createServiceSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    public function getServiceSchedule($id)
    {
        try {
            $schedule = $this->serviceService->getServiceSchedule($id);
            if (!$schedule) {
                http_response_code(404);
                return ['error' => 'Service Schedule not found'];
            }
            return json_decode($this->serializer->serialize($schedule, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getServiceSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateServiceSchedule($id, $data)
    {
        try {
            $schedule = $this->serviceService->updateServiceSchedule($id, $data);
            return ['id' => $schedule->getId(), 'message' => 'Service Schedule updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateServiceSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteServiceSchedule($id)
    {
        try {
            $this->serviceService->deleteServiceSchedule($id);
            return ['message' => 'Service Schedule deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteServiceSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllServiceSchedules()
    {
        try {
            $schedules = $this->serviceService->getServiceSchedulesByServiceId($serviceId);
            return json_decode($this->serializer->serialize($schedules, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllServiceSchedules: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailableSlots($serviceId)
    {
        try {
            $service = $this->serviceService->getService($serviceId);
            if (!$service) {
                http_response_code(404);
                return ['error' => 'Service not found'];
            }

            $schedules = $this->serviceService->getServiceSchedulesByServiceId($serviceId);
            $slots = [];

            foreach ($schedules as $schedule) {
                $currentRegistrations = $this->entityManager->getRepository(ServiceRegistrationModel::class)
                    ->count(['service_id' => $serviceId, 'schedule_id' => $schedule->getId()]);
                
                $slots[] = [
                    'schedule_id' => $schedule->getId(),
                    'start_time' => $schedule->getStartTime()->format('Y-m-d H:i:s'),
                    'end_time' => $schedule->getEndTime()->format('Y-m-d H:i:s'),
                    'capacity' => $service->getCapacity(),
                    'available_slots' => max(0, $service->getCapacity() - $currentRegistrations),
                ];
            }

            return $slots;
        } catch (\Exception $e) {
            error_log("Exception in getAvailableSlots: " . $e->getMessage());
            throw $e;
        }
    }
}
?>