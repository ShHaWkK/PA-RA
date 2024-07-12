<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
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
                    switch ($uriParts[1]) {
                        case 'registerVolunteer':
                            return $this->registerVolunteer($input);
                        case 'registerMerchant':
                            return $this->registerMerchant($input);
                        case 'addAvailability':
                            return $this->addAvailability($input);
                        default:
                            http_response_code(400);
                            return ['error' => 'Invalid endpoint'];
                    }
                } else {
                    http_response_code(400);
                    return ['error' => 'Invalid endpoint'];
                } // Break for POST case

            case 'GET':
                if (isset($uriParts[1])) {
                    switch ($uriParts[1]) {
                        case 'generatePlanning':
                            return $this->generatePlanning();
                        case 'role':
                            return $this->getByRole($input);
                        case 'status':
                            return $this->getByStatus($input);
                        default:
                            return $this->getUser($uriParts[1]);
                    }
                } else {
                    return $this->getAllUsers();
                } // Break for GET case

            case 'PUT':
                if (isset($uriParts[2])) {
                    return $this->updateUserStatus($uriParts[2], $input);
                } else {
                    http_response_code(400);
                    return ['error' => 'User ID not specified'];
                } // Break for PUT case

            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed']; // Break for default case
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
        $this->entityManager->beginTransaction();

        try {
            if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password']) || !isset($data['company_name'])) {
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
            $this->companyService->addCompany($data);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return ['id' => $user->getId(), 'message' => 'Merchant registered successfully. Awaiting approval.'];

        } catch (\Exception $e) {
            $this->entityManager->rollback();

            error_log("Exception in registerMerchant: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    /**
     * @throws NotSupported
     */
    private function getAllUsers()
    {
        $users = $this->entityManager->getRepository(UserModel::class)->findAll();

        // Prepare data using jsonSerialize() method
        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = $user->jsonSerialize();
        }

        return $serializedUsers;
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

    private function getByStatus($data)
    {
        $userRepository = $this->entityManager->getRepository(UserModel::class);
        $criteria = [];

        if (isset($data['role'])){
            $criteria['role'] = $data['role'];

        }
            $criteria['status'] = $data['status'];

        $users = $userRepository->findBy($criteria);


        // Prepare data using jsonSerialize() method
        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = $user->jsonSerialize();
        }

        return $serializedUsers;
    }

    private function getByRole($data)
    {
        $userRepository = $this->entityManager->getRepository(UserModel::class);

        $users = $userRepository->findBy(['role' => $data['role']]);

        // Prepare data using jsonSerialize() method
        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = $user->jsonSerialize();
        }

        return $serializedUsers;
    }
}
?>