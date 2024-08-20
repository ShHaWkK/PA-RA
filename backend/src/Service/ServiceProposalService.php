<?php
// Path: backend/src/Service/ServiceProposalService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\ServiceProposalModel;
use Entity\UserModel;
use Service\EmailService;

class ServiceProposalService
{
    private $entityManager;
    private $emailService;

    public function __construct(EntityManager $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
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

        // Envoi de l'email de notification
        $this->emailService->sendEmail(
            $user->getEmail(),
            'NO MORE WASTE - Votre proposition de service a été reçue',
            'Cher ' . $user->getFirstName() . ',<br><br>' .
            'Nous vous remercions chaleureusement pour votre proposition de service intitulée "' . $proposal->getName() . '".<br><br>' .
            'Votre proposition a bien été prise en compte et sera étudiée attentivement par notre équipe. Nous vous tiendrons informé(e) de la suite donnée à votre initiative.<br><br>' .
            'Votre engagement auprès de NO MORE WASTE est essentiel pour notre lutte contre le gaspillage. Merci pour votre contribution !<br><br>' .
            'Cordialement,<br>L\'équipe NO MORE WASTE'
        );

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
    // vous avez supprimé votre proposition de service + envoi d'email
    public function deleteProposal($id)
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
    public function getAllProposals()
    {
        return $this->entityManager->getRepository(ServiceProposalModel::class)->findAll();
    }

    public function getProposalsByUser($userId)
    {
        return $this->entityManager->getRepository(ServiceProposalModel::class)
            ->findBy(['created_by' => $userId]);
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
