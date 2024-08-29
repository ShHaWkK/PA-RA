<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Entity\CompanyModel;
use Entity\UserCompanyModel;
use Entity\UserModel;
use Entity\TicketModel;
use Doctrine\ORM\Exception\NotSupported;
use SplFileInfo;
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
    private $publicDir;

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
        $this->publicDir = __DIR__ . '/../../public';
    }


    public function processRequest($method, $uriParts, $input)
    {
        switch ($method) {
            case 'POST':
                if (isset($uriParts[1])) {
                    switch ($uriParts[1]) {
                        case 'registerVolunteer':
                            // Gérer la requête multipart/form-data pour extraire le JSON et le fichier
                            $input = $this->handleMultipartRequest();
                            return $this->registerVolunteer($input);
                        case 'registerMerchant':
                            $input = $this->handleMultipartRequest();
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
                    switch ($uriParts[1]) {
                        case 'generatePlanning':
                            return $this->generatePlanning();

                        case 'getSkills':
                            if (isset($uriParts[2])) {
                                return $this->getUserSkills($uriParts[2]);
                            } else {
                                http_response_code(400);
                                return ["message" => "User id not set"];
                            }

                        case 'getAvailabilities':
                            if (isset($uriParts[2])) {
                                return $this->getUserAvailabilities($uriParts[2]);
                            } else {
                                http_response_code(400);
                                return ["message" => "User id not set"];
                            }

                        case 'getUserCompanies':
                            if (isset($uriParts)){
                                return $this->getUserCompanies($uriParts[2]);
                            }else{
                                http_response_code(400);
                                return ["message" => "User id not set"];
                            }

                        case 'getUserFile':
                            if (isset($uriParts)){
                                return $this->getUserFile($uriParts[2]);
                            }else{
                                http_response_code(400);
                                return ["message" => "User id not set"];
                            }

                        case 'tickets':
                            return $this->getTicketsByUser((int)$uriParts[1]);

                        default:
                            return $this->getUser($uriParts[1]);
                    }
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
                    return $this->updateUser($uriParts[1],$input);
//                    http_response_code(400);
//                    return ['error' => 'User ID not specified'];
                }
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteUser($uriParts[1]);
                } else {
                    http_response_code(400);
                    return ['error' => 'User ID not specified'];
                }

            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    private function handleMultipartRequest()
    {
        $input = [];

        // Récupérer et décoder les données JSON du champ 'json_data'
        if (isset($_POST['json_data'])) {
            $jsonData = json_decode($_POST['json_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = $jsonData;
            } else {
                http_response_code(400);
                die(json_encode(['error' => 'Invalid JSON data']));
            }
        }

        // Gérer le fichier uploadé dans 'file_data'
        if (isset($_FILES['file_data']) && $_FILES['file_data']['error'] === UPLOAD_ERR_OK) {
            $pdfFile = $_FILES['file_data'];
            $uploadDir = $this->publicDir . '/UserFiles';

            // Assurer que le répertoire existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Générer un nom de fichier unique
            $filename = uniqid() . '.pdf';
            $filePath = $uploadDir . '/' . $filename;

            // Déplacer le fichier uploadé vers le répertoire de destination
            if (move_uploaded_file($pdfFile['tmp_name'], $filePath)) {
                // Ajouter le chemin du fichier au tableau d'entrée
                $input['file_path'] = '/UserFiles/' . $filename;
            } else {
                http_response_code(500);
                die(json_encode(['error' => 'Failed to save the uploaded file']));
            }
        }

        return $input;
    }

    private function registerVolunteer($data)
    {
        $this->entityManager->beginTransaction();

        try {
            // Validation des champs requis
            if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields'];
            }

            // Vérification si l'email existe déjà
            $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                http_response_code(409);
                return ['error' => 'Email already exists'];
            }

            // Générer un code de vérification
            $verificationCode = rand(100000, 999999);
            $data['verification_code'] = $verificationCode;
            $data['is_verified'] = false;

            // Créer et persister l'utilisateur
            $user = $this->userService->addUser($data, 'volunteer');

            // Affecter le chemin du fichier s'il est présent
            if (isset($data['file_path'])) {
                $user->setFilePath($data['file_path']);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Assigner les compétences si elles sont présentes
            if (isset($data['skills'])) {
                $this->skillService->addSkills($user, $data['skills']);
            }

            // Assigner les disponibilités si présentes
            if (isset($data['availabilities'])) {
                foreach ($data['availabilities'] as $availabilityData) {
                    if (!isset($availabilityData['day_of_week']) || !isset($availabilityData['start_time']) || !isset($availabilityData['end_time'])) {
                        http_response_code(400);
                        return ['error' => 'Missing required fields for availability'];
                    }
                    $availabilityData['user_id'] = $user->getId();
                    $this->availabilityService->addAvailability($availabilityData);
                }
            }

            // Commit transaction
            $this->entityManager->commit();

            // Envoyer l'email de vérification
            $this->emailService->sendVerificationEmail($data['email'], $verificationCode);

            return ['id' => $user->getId(), 'message' => 'Volunteer registered successfully. Verification code sent.'];

        } catch (\Exception $e) {
            // Rollback transaction en cas d'erreur
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
            // Validation des champs requis
            if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password']) || (!isset($data['company_id']) && !isset($data['company_name']))) {
                http_response_code(400);
                return ['error' => 'Missing required fields'];
            }

            // Vérification si l'email existe déjà
            $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                http_response_code(409);
                return ['error' => 'Email already exists'];
            }

            // Génération du code de vérification
            $verificationCode = rand(100000, 999999);
            $data['verification_code'] = $verificationCode;
            $data['is_verified'] = false;

            // Création de l'utilisateur
            $user = $this->userService->addUser($data, 'merchant');

            // Affecter le chemin du fichier s'il est présent
            if (isset($data['file_path'])) {
                $user->setFilePath($data['file_path']);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Gestion de l'association de l'entreprise
            if (isset($data['company_id'])) {
                // Si company_id est fourni, associer l'utilisateur à l'entreprise existante
                $company = $this->entityManager->getRepository(CompanyModel::class)->find($data['company_id']);
                if (!$company) {
                    http_response_code(404);
                    return ['error' => 'Company not found'];
                }
            } else {
                // Sinon, créer une nouvelle entreprise
                $company = $this->companyService->addCompany($data);
                $this->entityManager->persist($company);
                $this->entityManager->flush();
            }

            // Associer l'utilisateur à l'entreprise
            $userCompany = new UserCompanyModel();
            $userCompany->setUser($user)
                ->setCompany($company)
                ->setRole('merchant');
            $this->entityManager->persist($userCompany);

            // Finaliser la transaction
            $this->entityManager->flush();
            $this->entityManager->commit();

            // Envoyer l'email de vérification
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

    private function updateUserStatus($id, $data) {
        try {
            if (!isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing status field'];
            }

            // Assuming $entityManager is available to interact with the database
            $user = $this->entityManager->getRepository(UserModel::class)->find($id);

            if (!$user) {
                http_response_code(404);
                return ['error' => 'User not found'];
            }

            $user->setStatus($data['status']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return ['message' => 'User status updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateUserStatus: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }



    private function getAllUsers()
    {
        $users = $this->entityManager->getRepository(UserModel::class)->findAll();

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
        $data = $user->jsonSerialize();
        return $data;
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

    public function getUserSkills($userId)
    {
        $user = $this->entityManager->getRepository(UserModel::class)->find($userId);

        if (!$user) {
            http_response_code(404);
            return ['message' =>"User with ID $userId not found"];
        }

        $skills = $user->getSkills();

        if (!$skills) {
            http_response_code(404);
            return ['error' => 'Skill not found'];
        }

        $serializedSkills = [];
        foreach ($skills as $skill) {
            $serializedSkills[] = $skill->jsonSerialize();
        }

        return $serializedSkills;
    }

    private function getUserAvailabilities($userId)
    {
        $user = $this->entityManager->getRepository(UserModel::class)->find($userId);

        if (!$user) {
            http_response_code(404);
            return ['message' =>"User with ID $userId not found"];
        }

        $availabilities = $user->getAvailabilities();

        if (!$availabilities) {
            http_response_code(404);
            return ['error' => 'Availability not found'];
        }

        $serializedAvailabilities = [];
        foreach ($availabilities as $availability) {
            $serializedAvailabilities[] = $availability->jsonSerialize();
        }

        return $serializedAvailabilities;
    }

    public function getUserCompanies($userId)
    {
        $user = $this->entityManager->getRepository(UserModel::class)->find($userId);

        if (!$user) {
            http_response_code(404);
            return ['message' => "User with ID $userId not found"];
        }

        $companies = $user->getCompanies();
        if ($companies->isEmpty()) { // Vérifie si la collection est vide
            http_response_code(404);
            return ['error' => 'No companies found for this user'];
        }

        $serializedCompanies = [];
        foreach ($companies as $company) { // Correction de la syntaxe
            $serializedCompanies[] = $company->jsonSerialize();
        }

        return $serializedCompanies;
    }

    private function updateUser($id, $input)
    {
        try {
            // Verify if the input is an array
            if (!is_array($input)) {
                http_response_code(400);
                return ['error' => 'Invalid input format'];
            }

            // Fetch the user from the database
            $user = $this->entityManager->getRepository(UserModel::class)->find($id);

            // If the user is not found, return a 404 error
            if (!$user) {
                http_response_code(404);
                return ['error' => 'User not found'];
            }

            // Update the user fields with the provided input
            $user->updateFields($input);

            // Persist the changes and flush the entity manager
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Return a success message
            return ['message' => 'User updated successfully'];
        } catch (\Exception $e) {
            // Log the exception
            error_log($e->getMessage());

            // Return a 500 error in case of an exception
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    public function deleteUser(int $id)
    {
        $user = $this->entityManager->getRepository(UserModel::class)->find($id);

        if ($user === null) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return ['message' => 'User deleted successfully'];

        } catch (\Exception $e) {
            // Vous pouvez ajouter un logging ici pour l'erreur
            error_log("Erreur lors de la suppression de l'utilisateur avec l'ID $id : " . $e->getMessage());
            return false;
        }
    }

    private function getUserFile($userId)
    {
        try {
            // Récupérer l'utilisateur
            $user = $this->entityManager->find(UserModel::class, $userId);

            if (!$user) {
                http_response_code(404);
                return ["User with ID $userId not found."];
            }

            // Récupérer le chemin relatif du fichier
            $relativeFilePath = $user->getFilePath();

            if (!$relativeFilePath) {
                http_response_code(400);
                return ["Invalid file path. No file path associated with user ID $userId."];
            }

            // Construire le chemin absolu du fichier
            $absoluteFilePath = $this->publicDir . $relativeFilePath;

            // Vérifier si le fichier existe
            if (!file_exists($absoluteFilePath)) {
                http_response_code(404);
                return ["File not found at path $absoluteFilePath."];
            }

            // Obtenir les informations du fichier
            $fileInfo = new SplFileInfo($absoluteFilePath);
            $fileSize = $fileInfo->getSize();
            $fileName = $fileInfo->getBasename();
            $fileMimeType = mime_content_type($absoluteFilePath);

            // Définir les headers pour le téléchargement
            header('Content-Type: ' . $fileMimeType);
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . $fileSize);

            // Lire le fichier et l'envoyer au navigateur
            readfile($absoluteFilePath);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            return ["An error occurred while retrieving the file.", 'error' => $e->getMessage()];
        } catch (OptimisticLockException $e) {
        } catch (TransactionRequiredException $e) {
        } catch (ORMException $e) {
        }
    }

}
?>