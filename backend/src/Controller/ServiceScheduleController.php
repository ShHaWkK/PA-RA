<?php
// Path: backend/src/Controller/ServiceScheduleController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Service\ServiceScheduleService;

class ServiceScheduleController
{
    private $entityManager;
    private $serializer;
    private $serviceScheduleService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->serviceScheduleService = new ServiceScheduleService($entityManager);

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
                    return $this->createSchedule($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if (isset($uriParts[2]) && $uriParts[2] === 'byDate' && isset($_GET['date'])) {
                            return $this->getScheduleByDate((int) $uriParts[1], $_GET['date']);
                        }
                        return $this->getScheduleByUser((int) $uriParts[1]);
                    } else {
                        return $this->getAllSchedules();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateSchedule((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Schedule ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteSchedule((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Schedule ID not specified'];
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

    public function getScheduleByUser($userId)
    {
        try {
            $schedules = $this->serviceScheduleService->getScheduleByUser($userId);
            if (!$schedules) {
                http_response_code(404);
                return ['error' => 'No schedules found for this user'];
            }
            return json_decode($this->serializer->serialize($schedules, 'json'), true);
        } catch (\Exception $e) {
            error_log("Detailed Error in getScheduleByUser: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Error retrieving schedules for user ID: ' . $userId];
        }
    }
    private function createSchedule($data)
    {
        try {
            $schedule = $this->serviceScheduleService->createSchedule($data);
            return ['id' => $schedule->getId(), 'message' => 'Schedule created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createSchedule: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function getAllSchedules()
    {
        try {
            $schedules = $this->serviceScheduleService->getAllSchedules();
            return json_decode($this->serializer->serialize($schedules, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllSchedules: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function updateSchedule($id, $data)
    {
        try {
            $schedule = $this->serviceScheduleService->updateSchedule($id, $data);
            return ['id' => $schedule->getId(), 'message' => 'Schedule updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateSchedule: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function deleteSchedule($id)
    {
        try {
            $this->serviceScheduleService->deleteSchedule($id);
            return ['message' => 'Schedule deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteSchedule: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    public function getScheduleByDate($userId, $date)
{
    try {
        $schedules = $this->serviceScheduleService->getScheduleByDate($userId, $date);
        if (!$schedules) {
            http_response_code(404);
            return ['error' => "No schedules found for user ID: $userId on date: $date"];
        }
        return json_decode($this->serializer->serialize($schedules, 'json'), true);
    } catch (\Exception $e) {
        error_log("Detailed Error in getScheduleByDate: " . $e->getMessage());
        http_response_code(500);
        return ['error' => 'Error retrieving schedules for user ID: ' . $userId . ' on date: ' . $date];
    }
}
}
?>
