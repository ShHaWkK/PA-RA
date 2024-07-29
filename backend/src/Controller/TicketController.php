<?php
namespace Controller;

use Entity\TicketModel;
use Entity\UserModel;
use Entity\MessageModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Service\EmailService;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketController
{
    private $entityManager;
    private $serializer;
    private $emailService;

    public function __construct(EntityManager $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
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
            error_log("TicketController - processRequest called with method: $method");

            if (isset($uriParts[1]) && $uriParts[1] === 'messages') {
                // Redirect to MessageController
                $messageController = new MessageController($this->entityManager);
                return $messageController->processRequest($method, array_slice($uriParts, 1), $input);
            }

            switch ($method) {
                case 'POST':
                    return $this->createTicket($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if ($uriParts[1] === 'search') {
                            return $this->searchTickets($input);
                        } elseif ($uriParts[1] === 'search_admin') {
                            return $this->searchAdminByName($input['name']);
                        } elseif ($uriParts[1] === 'admins') {
                            return $this->getAllAdmins();
                        } else {
                            return $this->getTicket((int)$uriParts[0]);
                        }
                    } else {
                        return $this->getAllTickets();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        if ($uriParts[1] === 'assign') {
                            return $this->assignAdminToTicket((int)$uriParts[0], $input);
                        } elseif ($uriParts[1] === 'close') {
                            return $this->closeTicket((int)$uriParts[0], $input);
                        } else {
                            return $this->updateTicket((int)$uriParts[0], $input);
                        }
                    }
                    http_response_code(400);
                    return new JsonResponse(['error' => 'Ticket ID not specified']);
                case 'DELETE':
                    if (isset($uriParts[0])) {
                        return $this->deleteTicket((int)$uriParts[0]);
                    }
                    http_response_code(400);
                    return new JsonResponse(['error' => 'Ticket ID not specified']);
                default:
                    http_response_code(405);
                    return new JsonResponse(['error' => 'Method Not Allowed']);
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            http_response_code(500);
            return new JsonResponse(['error' => 'Internal Server Error']);
        }
    }


    

 public function createTicket($data)
    {
        try {
            error_log("Data received for creating ticket: " . json_encode($data));

            if (!isset($data['type']) || !isset($data['description']) || !isset($data['status']) || !isset($data['created_by'])) {
                http_response_code(400);
                return new JsonResponse(['error' => 'Missing required fields for new ticket']);
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
                if (is_string($data['attachments'])) {
                    $attachments = json_decode($data['attachments'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        return new JsonResponse(['error' => 'Invalid JSON in attachments']);
                    }
                } else {
                    $attachments = $data['attachments'];
                }
                $ticket->setAttachments($attachments);
            } else {
                $ticket->setAttachments(null);
            }

            $ticket->setCreatedAt(new \DateTime("now"));
            $ticket->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            // Envoyer un email de confirmation
            $user = $ticket->getCreatedBy();
            $this->sendConfirmationEmail($user->getEmail(), $ticket);

            $response = json_encode(['id' => $ticket->getId(), 'message' => 'Ticket created successfully']);
            error_log("Response: " . $response);
            return new JsonResponse(['id' => $ticket->getId(), 'message' => 'Ticket created successfully']);
        } catch (\Exception $e) {
            error_log("Exception in createTicket: " . $e->getMessage());
            http_response_code(500);
            return new JsonResponse(['error' => 'Internal Server Error']);
        }
    }

    private function sendConfirmationEmail($email, $ticket)
    {
        $subject = "Confirmation de création de ticket";
        $body = "Votre ticket a bien été pris en compte. Un administrateur va traiter votre demande sous peu.\n\nDétails du ticket:\n\nID: {$ticket->getId()}\nType: {$ticket->getType()}\nDescription: {$ticket->getDescription()}";
        $this->emailService->sendEmail($email, $subject, $body);
    }


    //------------------------ Obtenir un ticket ------------------------//
    public function getTicket($id)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
            }
            return json_decode($this->serializer->serialize($ticket, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    //------------------------ Mettre à jour un ticket ------------------------//

    public function updateTicket($id, $data)
    {
        try {
            if (!isset($data['type']) && !isset($data['description']) && !isset($data['status']) && !isset($data['assigned_to']) && !isset($data['attachments'])) {
                http_response_code(400);
                return json_encode(['error' => 'No fields to update']);
            }

            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
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
                if (!is_array($data['attachments'])) {
                    http_response_code(400);
                    return json_encode(['error' => 'Attachments must be a valid JSON array']);
                }
                $ticket->setAttachments($data['attachments']);
            }
            $ticket->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return json_encode(['id' => $ticket->getId(), 'message' => 'Ticket updated successfully']);
        } catch (\Exception $e) {
            error_log("Exception in updateTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }
    //------------------------ Supprimer un ticket ------------------------//
    public function deleteTicket($id)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $id);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
            }

            $this->entityManager->remove($ticket);
            $this->entityManager->flush();

            return json_encode(['message' => 'Ticket deleted successfully']);
        } catch (\Exception $e) {
            error_log("Exception in deleteTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    //------------------------ Voir tous les admins ------------------------//
        public function getAllAdmins()
    {
        try {
            $repository = $this->entityManager->getRepository(UserModel::class);
            $admins = $repository->findBy(['role' => 'admin']);

            if (!$admins) {
                http_response_code(404);
                return json_encode(['error' => 'No admins found']);
            }

            $result = [];
            foreach ($admins as $admin) {
                $result[] = [
                    'id' => $admin->getId(),
                    'firstName' => $admin->getFirstName(),
                    'lastName' => $admin->getLastName()
                ];
            }

            return json_encode($result);
        } catch (\Exception $e) {
            error_log("Exception in getAllAdmins: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    //------------------------ Voir tous les tickets ------------------------//
    public function getAllTickets()
    {
        try {
            $ticketRepository = $this->entityManager->getRepository(TicketModel::class);
            $tickets = $ticketRepository->findAll();
            
            // Préparez une réponse simplifiée ne contenant que les informations de tickets
            $ticketData = [];
            foreach ($tickets as $ticket) {
                $ticketData[] = [
                    'id' => $ticket->getId(),
                    'type' => $ticket->getType(),
                    'description' => $ticket->getDescription(),
                    'status' => $ticket->getStatus(),
                    'createdAt' => $ticket->getCreatedAt()->format('Y-m-d H:i:s'), // Ensure proper format
                    'updatedAt' => $ticket->getUpdatedAt()->format('Y-m-d H:i:s'), // Ensure proper format
                    'assignedTo' => $ticket->getAssignedTo() ? $ticket->getAssignedTo()->getId() : null,
                    'createdBy' => $ticket->getCreatedBy() ? $ticket->getCreatedBy()->getId() : null
                ];
            }

            // Sérialisez les données des tickets pour les renvoyer au client
            $response = json_encode($ticketData);
            error_log("All tickets retrieved: " . $response); // Log tickets retrieved
            return $response;
        } catch (\Exception $e) {
            error_log("Exception in getAllTickets: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
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
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function assignAdminToTicket($ticketId, $data)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
            }
    
            $admin = $this->entityManager->find(UserModel::class, $data['admin_id']);
            if (!$admin) {
                http_response_code(404);
                return json_encode(['error' => 'Admin not found']);
            }
    
            $ticket->setAssignedTo($admin);
            $ticket->setUpdatedAt(new \DateTime("now"));
            $this->entityManager->flush();
    
            return json_encode(['id' => $ticket->getId(), 'message' => 'Admin assigned to ticket successfully']);
        } catch (\Exception $e) {
            error_log("Exception in assignAdminToTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }
    
    public function searchAdminByName($name)
    {
        try {
            $repository = $this->entityManager->getRepository(UserModel::class);
            $admins = $repository->createQueryBuilder('u')
                                 ->where('u.role = :role')
                                 ->andWhere('u.firstName LIKE :name OR u.lastName LIKE :name')
                                 ->setParameter('role', 'admin')
                                 ->setParameter('name', '%' . $name . '%')
                                 ->getQuery()
                                 ->getResult();
    
            if (!$admins) {
                http_response_code(404);
                return json_encode(['error' => 'No admins found with that name']);
            }
    
            $result = [];
            foreach ($admins as $admin) {
                $result[] = [
                    'id' => $admin->getId(),
                    'firstName' => $admin->getFirstName(),
                    'lastName' => $admin->getLastName()
                ];
            }
    
            return json_encode($result);
        } catch (\Exception $e) {
            error_log("Exception in searchAdminByName: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function getTicketsByUser($userId)
    {
        try {
            // Assurez-vous que vous avez bien un utilisateur valide
            $user = $this->entityManager->find(UserModel::class, $userId);
            if (!$user) {
                http_response_code(404);
                return json_encode(['error' => 'User not found']);
            }
    
            // Récupérez les tickets créés par cet utilisateur
            $tickets = $this->entityManager->getRepository(TicketModel::class)->findBy(['created_by' => $user]);
    
            // Préparez une réponse simplifiée ne contenant que les informations de tickets
            $ticketData = [];
            foreach ($tickets as $ticket) {
                $ticketData[] = [
                    'id' => $ticket->getId(),
                    'type' => $ticket->getType(),
                    'description' => $ticket->getDescription(),
                    'status' => $ticket->getStatus(),
                    'createdAt' => $ticket->getCreatedAt()->format('Y-m-d H:i:s'), // Ensure proper format
                    'updatedAt' => $ticket->getUpdatedAt()->format('Y-m-d H:i:s'), // Ensure proper format
                    'assignedTo' => $ticket->getAssignedTo() ? $ticket->getAssignedTo()->getId() : null,
                    'attachments' => $ticket->getAttachments()
                ];
            }
    
            // Sérialisez les données des tickets pour les renvoyer au client
            $response = json_encode($ticketData);
            error_log("Tickets retrieved for user {$userId}: " . $response); // Log tickets retrieved
            return $response;
        } catch (\Exception $e) {
            error_log("Exception in getTicketsByUser: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function closeTicket($ticketId, $data)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
            }

            if ($ticket->getCreatedBy()->getId() !== $data['user_id'] && !$data['is_admin']) {
                http_response_code(403);
                return json_encode(['error' => 'Unauthorized action']);
            }

            $ticket->setStatus('closed');
            $ticket->setUpdatedAt(new \DateTime("now"));
            $this->entityManager->flush();

            return json_encode(['id' => $ticket->getId(), 'message' => 'Ticket closed successfully']);
        } catch (\Exception $e) {
            error_log("Exception in closeTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }


    public function reassignTicket($ticketId, $data)
    {
        try {
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                http_response_code(404);
                return json_encode(['error' => 'Ticket not found']);
            }

            $newAdmin = $this->entityManager->find(UserModel::class, $data['new_admin_id']);
            if (!$newAdmin) {
                http_response_code(404);
                return json_encode(['error' => 'Admin not found']);
            }

            $ticket->setAssignedTo($newAdmin);
            $ticket->setUpdatedAt(new \DateTime("now"));
            $this->entityManager->flush();

            return json_encode(['id' => $ticket->getId(), 'message' => 'Ticket reassigned successfully']);
        } catch (\Exception $e) {
            error_log("Exception in reassignTicket: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }
    }
}
?>