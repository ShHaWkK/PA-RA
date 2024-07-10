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
}
?>
