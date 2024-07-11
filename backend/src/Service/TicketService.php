<?php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\TicketModel;
use Entity\User;

class TicketService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTicket($data)
    {
        $ticket = new TicketModel();
        $ticket->setType($data['type']);
        $ticket->setDescription($data['description']);
        $ticket->setStatus($data['status']);
        $ticket->setCreatedBy($this->entityManager->find(User::class, $data['created_by']));
        if (isset($data['assigned_to'])) {
            $ticket->setAssignedTo($this->entityManager->find(User::class, $data['assigned_to']));
        }
        if (isset($data['attachments'])) {
            $ticket->setAttachments($data['attachments']);
        }
        $ticket->setCreatedAt(new \DateTime("now"));
        $ticket->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    public function getTicket($id)
    {
        return $this->entityManager->find(TicketModel::class, $id);
    }

    public function updateTicket($id, $data)
    {
        $ticket = $this->entityManager->find(TicketModel::class, $id);
        if (!$ticket) {
            throw new \Exception('Ticket not found');
        }

        if (isset($data['type'])) {
            $ticket->setType($data['type']);
        }
        if (isset($data['description'])) {
            $ticket->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $ticket->setStatus($data['status']);
        }
        if (isset($data['assigned_to'])) {
            $ticket->setAssignedTo($this->entityManager->find(User::class, $data['assigned_to']));
        }
        if (isset($data['attachments'])) {
            $ticket->setAttachments($data['attachments']);
        }
        $ticket->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return $ticket;
    }

    public function deleteTicket($id)
    {
        $ticket = $this->entityManager->find(TicketModel::class, $id);
        if (!$ticket) {
            throw new \Exception('Ticket not found');
        }

        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
    }

    public function getAllTickets()
    {
        return $this->entityManager->getRepository(TicketModel::class)->findAll();
    }

    public function searchTickets($criteria)
    {
        $repository = $this->entityManager->getRepository(TicketModel::class);
        $queryBuilder = $repository->createQueryBuilder('t');

        if (!empty($criteria['keyword'])) {
            $queryBuilder->andWhere('t.description LIKE :keyword')
                         ->setParameter('keyword', '%' . $criteria['keyword'] . '%');
        }

        if (!empty($criteria['status'])) {
            $queryBuilder->andWhere('t.status = :status')
                         ->setParameter('status', $criteria['status']);
        }

        if (!empty($criteria['createdAfter'])) {
            $queryBuilder->andWhere('t.createdAt >= :createdAfter')
                         ->setParameter('createdAfter', new \DateTime($criteria['createdAfter']));
        }

        if (!empty($criteria['createdBy'])) {
            $queryBuilder->andWhere('t.created_by = :createdBy')
                         ->setParameter('createdBy', $criteria['createdBy']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function autoAssignTicket($ticketId)
    {
        $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
        if (!$ticket) {
            throw new \Exception('Ticket not found');
        }

        $admins = $this->entityManager->getRepository(User::class)
                     ->findBy(['role' => 'admin', 'status' => 'active']);
        
        if (empty($admins)) {
            throw new \Exception('No available admins');
        }

        $ticket->setAssignedTo($admins[0]);
        $ticket->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return $ticket;
    }
}

?>
