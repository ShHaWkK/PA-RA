<?php
// Path: backend/src/Controller/UserController.php
namespace Controller;

use Entity\UserModel;
use Entity\UserSkillModel;
use Entity\CompanyModel;
use Entity\UserCompanyModel;
use Entity\SkillModel;
use Entity\AvailabilityModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController
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
        if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password']) || !isset($data['skills']) || !isset($data['availabilities'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        // Check if the email already exists
        $existingUser = $this->entityManager->getRepository(UserModel::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            http_response_code(400);
            return ['error' => 'Email already exists'];
        }

        $user = new UserModel();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone_number']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setRole('volunteer');
        $user->setStatus('pending');
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Handle skills assignment
        foreach ($data['skills'] as $skillId) {
            $skill = $this->entityManager->find(SkillModel::class, $skillId);
            if (!$skill) {
                http_response_code(400);
                return ['error' => 'Invalid skill ID: ' . $skillId];
            }

            $userSkill = new UserSkillModel();
            $userSkill->setUserId($user->getId());
            $userSkill->setSkillId($skill->getId());
            $this->entityManager->persist($userSkill);
        }

        // Handle availabilities assignment
        foreach ($data['availabilities'] as $availabilityData) {
            if (!isset($availabilityData['day_of_week']) || !isset($availabilityData['start_time']) || !isset($availabilityData['end_time'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for availability'];
            }

            $availability = new AvailabilityModel();
            $availability->setUser($user);
            $availability->setDayOfWeek($availabilityData['day_of_week']);
            $availability->setStartTime(new \DateTime($availabilityData['start_time']));
            $availability->setEndTime(new \DateTime($availabilityData['end_time']));
            $availability->setCreatedAt(new \DateTime("now"));
            $availability->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($availability);
        }

        $this->entityManager->flush();

        return ['id' => $user->getId(), 'message' => 'Volunteer registered successfully. Awaiting approval.'];
    }

    private function addAvailability($data)
    {
        if (!isset($data['user_id']) || !isset($data['day_of_week']) || !isset($data['start_time']) || !isset($data['end_time'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for availability'];
        }

        $user = $this->entityManager->find(UserModel::class, $data['user_id']);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        $availability = new AvailabilityModel();
        $availability->setUser($user);
        $availability->setDayOfWeek($data['day_of_week']);
        $availability->setStartTime(new \DateTime($data['start_time']));
        $availability->setEndTime(new \DateTime($data['end_time']));
        $availability->setCreatedAt(new \DateTime("now"));
        $availability->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($availability);
        $this->entityManager->flush();

        return ['id' => $availability->getId(), 'message' => 'Availability added successfully'];
    }

    private function generatePlanning()
{
    $volunteers = $this->entityManager->getRepository(UserModel::class)->findBy(['role' => 'volunteer', 'status' => 'approved']);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'First Name');
    $sheet->setCellValue('B1', 'Last Name');
    $sheet->setCellValue('C1', 'Email');
    $sheet->setCellValue('D1', 'Phone Number');
    $sheet->setCellValue('E1', 'Skills');
    $sheet->setCellValue('F1', 'Availabilities');

    $row = 2;
    foreach ($volunteers as $volunteer) {
        $sheet->setCellValue('A' . $row, $volunteer->getFirstName());
        $sheet->setCellValue('B' . $row, $volunteer->getLastName());
        $sheet->setCellValue('C' . $row, $volunteer->getEmail());
        $sheet->setCellValue('D' . $row, $volunteer->getPhoneNumber());

        $skills = [];
        foreach ($volunteer->getSkills() as $skill) {
            $skills[] = $skill->getName();
        }
        $sheet->setCellValue('E' . $row, implode(', ', $skills));

        $availabilities = [];
        foreach ($volunteer->getAvailabilities() as $availability) {
            $availabilities[] = $availability->getDayOfWeek() . ' ' . $availability->getStartTime()->format('H:i') . '-' . $availability->getEndTime()->format('H:i');
        }
        $sheet->setCellValue('F' . $row, implode(', ', $availabilities));

        $row++;
    }

    $publicDir = __DIR__ . '/../public';
    $planningsDir = $publicDir . '/plannings';

    // Vérifie si le répertoire public/plannings existe, sinon le crée
    if (!file_exists($planningsDir)) {
        mkdir($planningsDir, 0777, true);
    }

    $filePath = $planningsDir . '/planning_' . date('Y-m-d') . '.xlsx';

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    return ['message' => 'Planning generated successfully', 'path' => $filePath];
}   
    

    private function registerMerchant($data)
    {
        if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['phone_number']) || !isset($data['password']) || !isset($data['company_name']) || !isset($data['siret']) || !isset($data['address']) || !isset($data['renewal_date'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $user = new UserModel();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone_number']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setRole('merchant');
        $user->setStatus('pending');
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Handle company assignment
        $company = new CompanyModel();
        $company->setName($data['company_name']);
        $company->setSiret($data['siret']);
        $company->setAddress($data['address']);
        $company->setRenewalDate(new \DateTime($data['renewal_date']));
        $company->setRenewalStatus('pending');
        $company->setContactInfo($data['email']);
        $company->setCreatedAt(new \DateTime("now"));
        $company->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $userCompany = new UserCompanyModel();
        $userCompany->setUserId($user->getId());
        $userCompany->setCompanyId($company->getId());
        $userCompany->setRole('merchant');

        $this->entityManager->persist($userCompany);
        $this->entityManager->flush();

        return ['id' => $user->getId(), 'message' => 'Merchant registered successfully. Awaiting approval.'];
    }

    private function approveUser($data, $adminUserId)
    {
        if (!isset($data['user_id']) || !isset($data['status'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        // Check if the user is an admin
        $adminUser = $this->entityManager->find(UserModel::class, $adminUserId);
        if (!$adminUser || $adminUser->getRole() !== 'admin') {
            http_response_code(403);
            return ['error' => 'Only administrators can approve users'];
        }

        $user = $this->entityManager->find(UserModel::class, $data['user_id']);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        if ($data['status'] !== 'approved' && $data['status'] !== 'rejected') {
            http_response_code(400);
            return ['error' => 'Invalid status'];
        }

        $user->setStatus($data['status']);
        $user->setUpdatedAt(new \DateTime("now"));
        $this->entityManager->flush();

        return ['id' => $user->getId(), 'message' => 'User status updated to ' . $data['status']];
    }

    private function getUser($id)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }
        return json_decode($this->serializer->serialize($user, 'json'), true);
    }

    private function getAllUsers()
    {
        $users = $this->entityManager->getRepository(UserModel::class)->findAll();
        return json_decode($this->serializer->serialize($users, 'json'), true);
    }

    private function updateUserStatus($id, $data)
    {
        $user = $this->entityManager->find(UserModel::class, $id);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'User not found'];
        }

        if (isset($data['status'])) {
            $user->setStatus($data['status']);
            $user->setUpdatedAt(new \DateTime("now"));
            $this->entityManager->flush();
            return ['id' => $user->getId(), 'message' => 'User status updated successfully'];
        }

        http_response_code(400);
        return ['error' => 'Status not specified'];
    }
}
?>
