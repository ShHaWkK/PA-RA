<?php
// Path: backend/src/Controller/VolunteerController.php
namespace Controller;

use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class VolunteerController
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
                return $this->registerVolunteer($input);
            case 'GET':
                if (isset($uriParts[1])) {
                    return $this->getVolunteer((int) $uriParts[1]);
                } else {
                    return $this->getAllVolunteers();
                }
            case 'PUT':
                if (isset($uriParts[1])) {
                    return $this->updateVolunteer((int) $uriParts[1], $input);
                }
                http_response_code(400);
                return ['error' => 'Volunteer ID not specified'];
            case 'DELETE':
                if (isset($uriParts[1])) {
                    return $this->deleteVolunteer((int) $uriParts[1]);
                }
                http_response_code(400);
                return ['error' => 'Volunteer ID not specified'];
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function registerVolunteer($data)
    {
        if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields for new volunteer'];
        }

        $volunteer = new UserModel();
        $volunteer->setFirstName($data['first_name']);
        $volunteer->setLastName($data['last_name']);
        $volunteer->setEmail($data['email']);
        $volunteer->setPhoneNumber($data['phone_number'] ?? null);
        $volunteer->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $volunteer->setRole('volunteer');
        $volunteer->setStatus('pending');  // Status is set to pending for validation by admin
        $volunteer->setCreatedAt(new \DateTime("now"));
        $volunteer->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($volunteer);
        $this->entityManager->flush();

        return ['id' => $volunteer->getId(), 'message' => 'Volunteer registered successfully, pending approval'];
    }

    public function getVolunteer($id)
    {
        $volunteer = $this->entityManager->find(UserModel::class, $id);
        if (!$volunteer || $volunteer->getRole() !== 'volunteer') {
            http_response_code(404);
            return ['error' => 'Volunteer not found'];
        }
        return json_decode($this->serializer->serialize($volunteer, 'json'), true);
    }

    public function updateVolunteer($id, $data)
    {
        $volunteer = $this->entityManager->find(UserModel::class, $id);
        if (!$volunteer || $volunteer->getRole() !== 'volunteer') {
            http_response_code(404);
            return ['error' => 'Volunteer not found'];
        }

        if (isset($data['first_name'])) {
            $volunteer->setFirstName($data['first_name']);
        }
        if (isset($data['last_name'])) {
            $volunteer->setLastName($data['last_name']);
        }
        if (isset($data['email'])) {
            $volunteer->setEmail($data['email']);
        }
        if (isset($data['phone_number'])) {
            $volunteer->setPhoneNumber($data['phone_number']);
        }
        if (isset($data['password'])) {
            $volunteer->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['status'])) {
            $volunteer->setStatus($data['status']);
        }
        $volunteer->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return ['id' => $volunteer->getId(), 'message' => 'Volunteer updated successfully'];
    }

    public function deleteVolunteer($id)
    {
        $volunteer = $this->entityManager->find(UserModel::class, $id);
        if (!$volunteer || $volunteer->getRole() !== 'volunteer') {
            http_response_code(404);
            return ['error' => 'Volunteer not found'];
        }

        $this->entityManager->remove($volunteer);
        $this->entityManager->flush();

        return ['message' => 'Volunteer deleted successfully'];
    }

    public function getAllVolunteers()
    {
        $volunteerRepository = $this->entityManager->getRepository(UserModel::class);
        $volunteers = $volunteerRepository->findBy(['role' => 'volunteer']);
        return json_decode($this->serializer->serialize($volunteers, 'json'), true);
    }
}
?>
