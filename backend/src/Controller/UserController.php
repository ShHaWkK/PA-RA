<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Entity\UserModel;
use Entity\TicketModel;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Service\UserService;
use Service\CompanyService;
use Service\SkillService;
use Service\AvailabilityService;
use Service\EmailService;

class UserController
{
    private $entityManager;
    private $serializer;
    private $userService;
    private $companyService;
    private $skillService;
    private $availabilityService;
    private $emailService;

    public function __construct(EntityManager $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ])];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->userService = new UserService($entityManager);
        $this->companyService = new CompanyService($entityManager);
        $this->skillService = new SkillService($entityManager);
        $this->availabilityService = new AvailabilityService($entityManager);
        $this->emailService = $emailService;
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
                        case 'verifyCode':
                            return $this->verifyCode($input); 
                        default:
                            http_response_code(400);
                            return ['error' => 'Invalid endpoint'];
                    }
                } else {
                    http_response_code(400);
                    return ['error' => 'Invalid endpoint'];
                }

            case 'GET':
                if (isset($uriParts[1])) {
                    return match ($uriParts[1]) {
                        'generatePlanning' => $this->generatePlanning(),
                        'tickets' => $this->getTicketsByUser((int)$uriParts[1]),
                        default => $this->getUser($uriParts[1]),
                    };
                } else {
                    return $this->getUsersByCriteria($_GET);
                }

            case 'PUT':
                if (isset($uriParts[2])) {
                    switch ($uriParts[1]) {
                        case 'approval':
                            return $this->updateUserStatus($uriParts[2], $input);
                    }
                } else {
                    http_response_code(400);
                    return ['error' => 'User ID not specified'];
                }

            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }
    private function registerVolunteer($data)
    {
        $this->entityManager->beginTransaction();

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

            // Generate verification code
            $verificationCode = rand(100000, 999999);
            $data['verification_code'] = $verificationCode;
            $data['is_verified'] = false;

            $user = $this->userService->addUser($data, 'volunteer');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

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
            $this->entityManager->commit();

            // Send verification email
            $this->emailService->sendVerificationEmail($data['email'], $verificationCode);

            return ['id' => $user->getId(), 'message' => 'Volunteer registered successfully. Verification code sent.'];

        } catch (\Exception $e) {
            $this->entityManager->rollback();

            error_log("Exception in registerVolunteer: " . $e->getMessage());
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

            // Generate verification code
            $verificationCode = rand(100000, 999999);
            $data['verification_code'] = $verificationCode;
            $data['is_verified'] = false;

            $user = $this->userService->addUser($data, 'merchant');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Handle company assignment
            $this->companyService->addCompany($data);

            $this->entityManager->flush();
            $this->entityManager->commit();

            // Send verification email
            $this->emailService->sendVerificationEmail($data['email'], $verificationCode);

            return ['id' => $user->getId(), 'message' => 'Merchant registered successfully. Verification code sent.'];

        } catch (\Exception $e) {
            $this->entityManager->rollback();

            error_log("Exception in registerMerchant: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function verifyCode($data)
    {
        if (!isset($data['email']) || !isset($data['verification_code'])) {
            http_response_code(400);
            return ['error' => 'Missing email or verification code'];
        }

        $user = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        if ($user->getVerificationCode() === $data['verification_code']) {
            $user->setIsVerified(true);
            $user->setVerificationCode(null); // Clear the verification code after successful verification
            $this->entityManager->flush();
            return ['message' => 'Verification successful.'];
        } else {
            http_response_code(400);
            return ['error' => 'Invalid verification code'];
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

    private function getUsersByCriteria($data)
    {
        $userRepository = $this->entityManager->getRepository(UserModel::class);
        $criteria = [];

        if (isset($data['role'])) {
            $criteria['role'] = $data['role'];
        }
        if (isset($data['status'])) {
            $criteria['status'] = $data['status'];
        }

        $users = $userRepository->findBy($criteria);

        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = $user->jsonSerialize();
        }

        return $serializedUsers;
    }
    public function getTicketsByUser($userId)
{
    try {
        // Assurez-vous que vous avez bien un utilisateur valide
        $user = $this->entityManager->find(UserModel::class, $userId);
        if (!$user) {
            http_response_code(404);
            return json_encode(['error' => 'User not found']);
        }

        // Récupérez les tickets créés par cet utilisateur
        $tickets = $this->entityManager->getRepository(TicketModel::class)->findBy(['created_by' => $user]);

        // Préparez une réponse simplifiée ne contenant que les informations de tickets
        $ticketData = [];
        foreach ($tickets as $ticket) {
            $ticketData[] = [
                'id' => $ticket->getId(),
                'type' => $ticket->getType(),
                'description' => $ticket->getDescription(),
                'status' => $ticket->getStatus(),
                'createdAt' => $ticket->getCreatedAt(),
                'updatedAt' => $ticket->getUpdatedAt(),
                'assignedTo' => $ticket->getAssignedTo() ? $ticket->getAssignedTo()->getId() : null,
                'attachments' => $ticket->getAttachments()
            ];
        }

        // Sérialisez les données des tickets pour les renvoyer au client
        $response = json_encode($ticketData);
        error_log("Tickets retrieved for user {$userId}: " . $response); // Log tickets retrieved
        return $response;
    } catch (\Exception $e) {
        error_log("Exception in getTicketsByUser: " . $e->getMessage());
        http_response_code(500);
        return json_encode(['error' => 'Internal Server Error']);
    }
}

    
    
}

?>
