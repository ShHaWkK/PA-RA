<?php
// Path: backend/src/Service/ServiceProposalService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceProposalModel;
use Entity\UserModel;
use Entity\ServiceModel;

class ServiceProposalService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProposal($data, $createdById)
    {
        $user = $this->entityManager->find(UserModel::class, $createdById);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $proposal = new ServiceProposalModel();  // This should now work correctly
        $proposal->setName($data['name']);
        $proposal->setDescription($data['description']);
        $proposal->setStatus($data['status'] ?? 'proposed');
        $proposal->setCreatedBy($createdById);
        $proposal->setCreatedAt(new \DateTime());
        $proposal->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($proposal);
        $this->entityManager->flush();

        return $proposal;
    }

    public function getProposal($id)
    {
        return $this->entityManager->find(ServiceProposalModel::class, $id);
    }

    public function updateProposal($id, $data)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
        if (!$proposal) {
            throw new \Exception('Proposal not found');
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

        $this->entityManager->flush();

        return $proposal;
    }

    public function deleteProposal($id)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
        if (!$proposal) {
            throw new \Exception('Proposal not found');
        }

        $this->entityManager->remove($proposal);
        $this->entityManager->flush();
    }

    public function getAllProposals()
    {
        return $this->entityManager->getRepository(ServiceProposalModel::class)->findAll();
    }

    public function approveProposal($id)
    {
        $proposal = $this->entityManager->find(ServiceProposalModel::class, $id);
        if (!$proposal) {
            throw new \Exception('Proposal not found');
        }
    
        $proposal->setStatus('approved');
        $proposal->setUpdatedAt(new \DateTime());
    
        $this->entityManager->flush();
    
        return $proposal;
    }
}