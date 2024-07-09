<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Entity\UserModel;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Service\UserService;
use Service\CompanyService;
use Service\SkillService;
use Service\AvailabilityService;

class UserController
{
    private $entityManager;
    private $serializer;
    private $userService;
    private $companyService;
    private $skillService;
    private $availabilityService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->userService = new UserService($entityManager);
        $this->companyService = new CompanyService($entityManager);
        $this->skillService = new SkillService($entityManager);
        $this->availabilityService = new AvailabilityService($entityManager);
    }

    public function processRequest($method, $uriParts, $input)
    {
        switch ($method) {
            case 'POST':
                if (isset($uriParts[1])) {
                    if ($uriParts[1] === 'registerVolunteer') {
                        return $this->registerVolunteer($input);
                    } elseif ($uriParts[1] === 'registerMerchant') {
                        return $this->registerMerchant($input);
                    } elseif ($uriParts[1] === 'approveUser') {
                        return $this->approveUser($input, $uriParts[2] ?? null);
                    } elseif ($uriParts[1] === 'addAvailability') {
                        return $this->addAvailability($input);
                    }
                }
                http_response_code(400);
                return ['error' => 'Invalid endpoint'];
            case 'GET':
                if (isset($uriParts[1])) {
                    if ($uriParts[1] === 'generatePlanning') {
                        return $this->generatePlanning();
                    } else {
                        return $this->getUser($uriParts[1]);
                    }
                } else {
                    return $this->getAllUsers();
                }
            case 'PUT':
                if (isset($uriParts[1])) {
                    return $this->updateUserStatus($uriParts[1], $input);
                }
                http_response_code(400);
                return ['error' => 'User ID not specified'];
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    private function registerVolunteer($data)
    {
        try {
            if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields'];
            }

            // Check if the email already exists
            $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
            error_log("wesh");

            if ($existingUser) {
                http_response_code(409);
                return ['error' => 'Email already exists'];
            }

            $user = $this->userService->addUser($data, 'volunteer');

            // Handle skills assignment
            if (isset($data['skills'])) {
                $this->skillService->addSkills($user, $data['skills']);
            }

            // Handle availabilities assignment
            if (isset($data['availabilities'])) {
                foreach ($data['availabilities'] as $index => $availabilityData) {
                    if (!isset($availabilityData['day_of_week']) || !isset($availabilityData['start_time']) || !isset($availabilityData['end_time'])) {
                        http_response_code(400);
                        return ['error' => "Missing required fields for availability at index $index"];
                    }
                    // Add user_id to availability data
                    $availabilityData['user_id'] = $user->getId();
                    $this->availabilityService->addAvailability($availabilityData);
                }
            }

            $this->entityManager->flush();

            return ['id' => $user->getId(), 'message' => 'Volunteer registered successfully. Awaiting approval.'];

        } catch (\Exception $e) {
            $this->entityManager->rollback();

            error_log("Exception in registerVolunteer: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function addAvailability($data)
    {
        try {
            return $this->availabilityService->addAvailability($data);
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function generatePlanning()
    {
        $volunteers = $this->entityManager->getRepository(UserModel::class)->findBy(['role' => 'volunteer', 'status' => 'approved']);
        $availabilityRepository = $this->entityManager->getRepository(AvailabilityModel::class);

        $planning = [];

        foreach ($volunteers as $volunteer) {
            $availabilities = $availabilityRepository->findBy(['user' => $volunteer->getId()]);
            foreach ($availabilities as $availability) {
                $planning[$availability->getDayOfWeek()][] = [
                    'volunteer_id' => $volunteer->getId(),
                    'start_time' => $availability->getStartTime()->format('H:i'),
                    'end_time' => $availability->getEndTime()->format('H:i'),
                ];
            }
        }

        return $planning;
    }

    private function updateUserStatus($id, $data)
    {
        try {
            if (!isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing status field'];
            }

            $user = $this->userService->updateUserStatus($id, $data['status']);
            return ['message' => 'User status updated successfully'];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function registerMerchant($data)
    {
        try {
            if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password']) || !isset($data['company'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields'];
            }

            // Check if the email already exists
            $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                http_response_code(409);
                return ['error' => 'Email already exists'];
            }

            $user = $this->userService->addUser($data, 'merchant');

            // Handle company assignment
            $companyData = $data['company'];
            $this->companyService->addCompany($companyData, $user);

            $this->entityManager->flush();

            return ['id' => $user->getId(), 'message' => 'Merchant registered successfully. Awaiting approval.'];

        } catch (\Exception $e) {
            $this->entityManager->rollback();

            error_log("Exception in registerMerchant: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function approveUser($data, $userId)
    {
        try {
            if (!$userId) {
                http_response_code(400);
                return ['error' => 'User ID not provided'];
            }

            $user = $this->userService->updateUserStatus($userId, 'approved');
            return ['message' => 'User approved successfully'];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function getAllUsers()
    {
        $users = $this->entityManager->getRepository(UserModel::class)->findAll();
        $data = $this->serializer->serialize($users, 'json');
        return json_decode($data, true);
    }

    private function getUser($id)
    {
        $user = $this->entityManager->getRepository(UserModel::class)->find($id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }
        $data = $this->serializer->serialize($user, 'json');
        return json_decode($data, true);
    }
}
?>