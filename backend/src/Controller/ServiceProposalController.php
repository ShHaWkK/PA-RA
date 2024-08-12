<?php
// Path: backend/src/Controller/ServiceProposalController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Service\ServiceProposalService;
use Service\ServiceService;

class ServiceProposalController
{
    private $entityManager;
    private $serializer;
    private $serviceProposalService;
    private $serviceService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->serviceProposalService = new ServiceProposalService($entityManager);
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
                    return $this->createProposal($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getProposal((int) $uriParts[1]);
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

    private function createProposal($data)
    {
        try {
            if (!isset($data['name']) || !isset($data['description']) || !isset($data['created_by'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new proposal'];
            }

            $proposal = $this->serviceProposalService->createProposal($data, (int) $data['created_by']);

            return ['id' => $proposal->getId(), 'message' => 'Proposal created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createProposal: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
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
        try {
            $this->serviceProposalService->deleteProposal($id);
            return ['message' => 'Proposal deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteProposal: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
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
