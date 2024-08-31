<?php
// Path: backend/src/Controller/ServiceProposalController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Service\ServiceProposalService;
use Service\ServiceService;
use Service\EmailService;
use Entity\UserModel;
use Entity\ServiceProposalModel;

class ServiceProposalController
{
    private $entityManager;
    private $serializer;
    private $serviceProposalService;
    private $serviceService;
    private $emailService;  // Ensure this is declared

    public function __construct(EntityManager $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;  // Initialize it here
        $this->serviceProposalService = new ServiceProposalService($entityManager, $emailService);
        $this->serviceService = new ServiceService($entityManager);
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }



    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    if (isset($input['created_by'])) {
                        return $this->createProposal($input, (int) $input['created_by']);
                    } else {
                        http_response_code(400);
                        return ['error' => 'Missing required field: created_by'];
                    }
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getProposalsByUser((int) $uriParts[1]);
                    } else {
                        return $this->getAllProposals();
                    }
                case 'PUT':
                    if (isset($uriParts[1]) && isset($uriParts[2]) && $uriParts[2] === 'approve') {
                        return $this->approveAndCreateService((int) $uriParts[1]);
                    }
                    if (isset($uriParts[1])) {
                        return $this->updateProposal((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Proposal ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteProposal((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Proposal ID not specified'];
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
    
    private function getProposalsByUser($userId)
    {
        try {
            $proposals = $this->serviceProposalService->getProposalsByUser($userId);
            if (!$proposals) {
                http_response_code(404);
                return ['error' => 'No proposals found for this user'];
            }
            return json_decode($this->serializer->serialize($proposals, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getProposalsByUser: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    public function createProposal($data, $createdById)
    {
        $user = $this->entityManager->find(UserModel::class, $createdById);
        if (!$user) {
            throw new \Exception('User not found');
        }
    
        $proposal = new ServiceProposalModel();
        $proposal->setName($data['name']);
        $proposal->setDescription($data['description']);
        $proposal->setStatus($data['status'] ?? 'proposed');
        $proposal->setCreatedBy($createdById);
        $proposal->setCreatedAt(new \DateTime());
        $proposal->setUpdatedAt(new \DateTime());
    
        $this->entityManager->persist($proposal);
        $this->entityManager->flush();
    
        // Send notification email
        $this->emailService->sendEmail(
            $user->getEmail(),
            'NO MORE WASTE - Votre proposition de service a été reçue',
            'Cher ' . $user->getFirstName() . ',<br><br>' .
            'Nous vous remercions chaleureusement pour votre proposition de service intitulée "' . $proposal->getName() . '".<br><br>' .
            'Votre proposition a bien été prise en compte et sera étudiée attentivement par notre équipe. Nous vous tiendrons informé(e) de la suite donnée à votre initiative.<br><br>' .
            'Votre engagement auprès de NO MORE WASTE est essentiel pour notre lutte contre le gaspillage. Merci pour votre contribution !<br><br>' .
            'Cordialement,<br>L\'équipe NO MORE WASTE'
        );

        return ['id' => $proposal->getId(), 'message' => 'Proposal created successfully'];
    }
    

    private function getProposal($id)
    {
        try {
            $proposal = $this->serviceProposalService->getProposal($id);
            if (!$proposal) {
                http_response_code(404);
                return ['error' => 'Proposal not found'];
            }
            return json_decode($this->serializer->serialize($proposal, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getProposal: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function updateProposal($id, $data)
    {
        try {
            if (empty($data)) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $proposal = $this->serviceProposalService->updateProposal($id, $data);

            return ['id' => $proposal->getId(), 'message' => 'Proposal updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateProposal: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private function deleteProposal($id)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
        if (!$proposal) {
            throw new \Exception('Proposal not found');
        }

        // Retrieve the user who created the proposal
        $user = $this->entityManager->find(UserModel::class, $proposal->getCreatedBy());
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Remove the proposal from the database
        $this->entityManager->remove($proposal);
        $this->entityManager->flush();

        // Send an email notification to the user
        $this->emailService->sendEmail(
            $user->getEmail(),
            'NO MORE WASTE - Suppression de votre proposition de service',
            'Cher ' . $user->getFirstName() . ',<br><br>' .
            'Nous vous informons que votre proposition de service intitulée "' . $proposal->getName() . '" a été supprimée.<br><br>' .
            'Si vous avez des questions ou si vous souhaitez soumettre une nouvelle proposition, n\'hésitez pas à nous contacter.<br><br>' .
            'Cordialement,<br>L\'équipe NO MORE WASTE'
        );

        return ['message' => 'Proposal deleted and email notification sent'];
    }
    private function getAllProposals()
    {
        try {
            $proposals = $this->serviceProposalService->getAllProposals();
            return json_decode($this->serializer->serialize($proposals, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllProposals: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    private function approveAndCreateService($proposalId)
    {
        try {
            // Approve the proposal
            $proposal = $this->serviceProposalService->approveProposal($proposalId);
    
            // Create service from approved proposal
            $service = $this->serviceService->createServiceFromProposal($proposalId);
    
            return [
                'proposal_id' => $proposal->getId(),
                'service_id' => $service->getId(),
                'message' => 'Proposal approved and service created successfully'
            ];
        } catch (\Exception $e) {
            error_log("Exception in approveAndCreateService: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
    
    
}