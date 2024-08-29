<?php
// Path: backend/src/Service/ServiceScheduleService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceScheduleModel;
use Entity\ServiceRegistrationModel;

class ServiceScheduleService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function getScheduleByUser($userId)
    {
        try {
            $query = $this->entityManager->createQuery(
                'SELECT ss FROM Entity\ServiceScheduleModel ss
                 JOIN Entity\ServiceRegistrationModel sr WITH ss.service = sr.service
                 WHERE sr.user_id = :userId'
            )->setParameter('userId', $userId);
         
            $result = $query->getResult();
    
            if (empty($result)) {
                throw new \Exception("No schedules found for user ID: $userId");
            }
    
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getScheduleByUser for user ID: $userId - " . $e->getMessage());
            throw new \Exception("Error retrieving schedules for user ID: " . $userId);
        }
    }
    
    
    
    public function getAllSchedules()
    {
        try {
            $repository = $this->entityManager->getRepository(ServiceScheduleModel::class);
            return $repository->findAll();
        } catch (\Exception $e) {
            throw new \Exception("Error retrieving all schedules");
        }
    }

    public function createSchedule($data)
    {
        try {
            $schedule = new ServiceScheduleModel();
            $schedule->setService($data['service']);
            $schedule->setStartTime(new \DateTime($data['start_time']));
            $schedule->setEndTime(new \DateTime($data['end_time']));
            $schedule->setLocation($data['location']);

            $this->entityManager->persist($schedule);
            $this->entityManager->flush();

            return $schedule;
        } catch (\Exception $e) {
            throw new \Exception("Error creating schedule");
        }
    }

    public function updateSchedule($id, $data)
    {
        try {
            $schedule = $this->entityManager->find(ServiceScheduleModel::class, $id);
            if (!$schedule) {
                throw new \Exception("Schedule not found");
            }

            $schedule->setStartTime(new \DateTime($data['start_time']));
            $schedule->setEndTime(new \DateTime($data['end_time']));
            $schedule->setLocation($data['location']);

            $this->entityManager->flush();

            return $schedule;
        } catch (\Exception $e) {
            throw new \Exception("Error updating schedule ID: " . $id);
        }
    }

    public function deleteSchedule($id)
    {
        try {
            $schedule = $this->entityManager->find(ServiceScheduleModel::class, $id);
            if (!$schedule) {
                throw new \Exception("Schedule not found");
            }

            $this->entityManager->remove($schedule);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error deleting schedule ID: " . $id);
        }
    }

    public function getScheduleByDate($userId, $date)
    {
        try {
            $query = $this->entityManager->createQuery(
                'SELECT ss FROM Entity\ServiceScheduleModel ss
                 JOIN Entity\ServiceRegistrationModel sr WITH ss.service = sr.service
                 WHERE sr.user_id = :userId AND ss.start_time >= :dateStart AND ss.end_time < :dateEnd'
            )
            ->setParameter('userId', $userId)
            ->setParameter('dateStart', new \DateTime($date . ' 00:00:00'))
            ->setParameter('dateEnd', new \DateTime($date . ' 23:59:59'));
    
            $result = $query->getResult();
    
            if (empty($result)) {
                throw new \Exception("No schedules found for user ID: $userId on date: $date");
            }
    
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getScheduleByDate for user ID: $userId - " . $e->getMessage());
            throw new \Exception("Error retrieving schedules for user ID: $userId on date: $date");
        }
    }
    
    

}
?>
