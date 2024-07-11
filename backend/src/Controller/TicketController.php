<?php
namespace Controller;

use Entity\TicketModel;
use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class TicketController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ])];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createTicket($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if ($uriParts[1] === 'search') {
                            return $this->searchTickets($input);
                        } else {
                            return $this->getTicket((int)$uriParts[1]);
                        }
                    } else {
                        return $this->getAllTickets();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        if ($uriParts[1] === 'assign') {
                            return $this->autoAssignTicket((int)$uriParts[2]);
                        } else {
                            return $this->updateTicket((int)$uriParts[1], $input);
                        }
                    }
                    http_response_code(400);
                    return ['error' => 'Ticket ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteTicket((int)$uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Ticket ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createTicket($data)
    {
        try {
            if (!isset($data['type']) || !isset($data['description']) || !isset($data['status']) || !isset($data['created_by'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new ticket'];
            }

            $ticket = new TicketModel();
            $ticket->setType($data['type']);
            $ticket->setDescription($data['description']);
            $ticket->setStatus($data['status']);
            $ticket->setCreatedBy($this->entityManager->find(UserModel::class, $data['created_by']));
            if (isset($data['assigned_to'])) {
                $ticket->setAssignedTo($this->entityManager->find(UserModel::class, $data['assigned_to']));
            }
            if (isset($data['attachments'])) {
                $ticket->setAttachments($data['attachments']);
            }
            $ticket->setCreatedAt(new \DateTime("now"));
            $ticket->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            return ['id' => $ticket->getId(), 'message' => 'Ticket created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createTicket: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTicket($id)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return ['error' => 'Ticket not found'];
            }
            return json_decode($this->serializer->serialize($ticket, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getTicket: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateTicket($id, $data)
    {
        try {
            if (!isset($data['type']) && !isset($data['description']) && !isset($data['status']) && !isset($data['assigned_to'])) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return ['error' => 'Ticket not found'];
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
                $ticket->setAssignedTo($this->entityManager->find(UserModel::class, $data['assigned_to']));
            }
            if (isset($data['attachments'])) {
                $ticket->setAttachments($data['attachments']);
            }
            $ticket->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $ticket->getId(), 'message' => 'Ticket updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateTicket: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteTicket($id)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return ['error' => 'Ticket not found'];
            }

            $this->entityManager->remove($ticket);
            $this->entityManager->flush();

            return ['message' => 'Ticket deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteTicket: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllTickets()
    {
        try {
            $ticketRepository = $this->entityManager->getRepository(TicketModel::class);
            $tickets = $ticketRepository->findAll();
            return json_decode($this->serializer->serialize($tickets, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllTickets: " . $e->getMessage());
            throw $e;
        }
    }

    public function searchTickets($criteria)
    {
        try {
            $tickets = $this->entityManager->getRepository(TicketModel::class)->searchTickets($criteria);
            return json_decode($this->serializer->serialize($tickets, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in searchTickets: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

    public function autoAssignTicket($ticketId)
    {
        try {
            $ticket = $this->entityManager->getRepository(TicketModel::class)->autoAssignTicket($ticketId);
            return json_decode($this->serializer->serialize($ticket, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in autoAssignTicket: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }
}

?>
