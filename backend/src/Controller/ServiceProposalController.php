<?php
// Path: backend/src/Controller/ServiceProposalController.php
namespace Controller;

use Entity\ServiceProposalModel; 
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;

class ServiceProposalController
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
            throw $e;
        }
    }

    public function createProposal($data)
    {
        try {
            if (!isset($data['name']) || !isset($data['description']) || !isset($data['created_by']) || !isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new proposal'];
            }

            $proposal = new ServiceProposalModel();
            $proposal->setName($data['name']);
            $proposal->setDescription($data['description']);
            $proposal->setCreatedBy($data['created_by']);
            $proposal->setStatus($data['status']);
            $proposal->setCreatedAt(new \DateTime("now"));
            $proposal->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($proposal);
            $this->entityManager->flush();

            return ['id' => $proposal->getId(), 'message' => 'Proposal created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createProposal: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProposal($id)
    {
        try {
            $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
            if (!$proposal) {
                http_response_code(404);
                return ['error' => 'Proposal not found'];
            }
            return json_decode($this->serializer->serialize($proposal, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getProposal: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProposal($id, $data)
    {
        try {
            if (!isset($data['name']) && !isset($data['description']) && !isset($data['status'])) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
            if (!$proposal) {
                http_response_code(404);
                return ['error' => 'Proposal not found'];
            }

            if (isset($data['name'])) {
                $proposal->setName($data['name']);
            }
            if (isset($data['description'])) {
                $proposal->setDescription($data['description']);
            }
            if (isset($data['status'])) {
                $proposal->setStatus($data['status']);
            }
            $proposal->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $proposal->getId(), 'message' => 'Proposal updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateProposal: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProposal($id)
    {
        try {
            $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
            if (!$proposal) {
                http_response_code(404);
                return ['error' => 'Proposal not found'];
            }

            $this->entityManager->remove($proposal);
            $this->entityManager->flush();

            return ['message' => 'Proposal deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteProposal: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllProposals()
    {
        try {
            $proposalRepository = $this->entityManager->getRepository(ServiceProposalModel::class);
            $proposals = $proposalRepository->findAll();
            return json_decode($this->serializer->serialize($proposals, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllProposals: " . $e->getMessage());
            throw $e;
        }
    }
}

?>
