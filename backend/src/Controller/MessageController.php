<?php
namespace Controller;

use Entity\MessageModel;
use Entity\TicketModel;
use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        error_log("MessageController instancié avec succès.");
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            error_log("MessageController - processRequest called with method: $method");
            switch ($method) {
                case 'POST':
                    if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
                        return $this->addMessage((int)$uriParts[1], $input);
                    }
                    http_response_code(400);
                    return new JsonResponse(['error' => 'Ticket ID not specified']);
                case 'GET':
                    if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
                        return $this->getTicketMessages((int)$uriParts[1]);
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

    public function addMessage($ticketId, $data)
    {
        try {
            error_log("MessageController - Data received for creating message: " . json_encode($data));

            if (!isset($data['author_id']) || !isset($data['recipient_id']) || !isset($data['content'])) {
                return new JsonResponse(['error' => 'Missing required fields for new message'], 400);
            }

            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                return new JsonResponse(['error' => 'Ticket not found'], 404);
            }

            $author = $this->entityManager->find(UserModel::class, $data['author_id']);
            if (!$author) {
                return new JsonResponse(['error' => 'Author not found'], 404);
            }

            $recipient = $this->entityManager->find(UserModel::class, $data['recipient_id']);
            if (!$recipient) {
                return new JsonResponse(['error' => 'Recipient not found'], 404);
            }

            $message = new MessageModel();
            $message->setTicket($ticket);
            $message->setAuthor($author);
            $message->setRecipient($recipient);
            $message->setContent($data['content']);
            $message->setCreatedAt(new \DateTime("now"));

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Message added successfully', 'message_id' => $message->getId()]);
        } catch (\Exception $e) {
            error_log("Exception in addMessage: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getTicketMessages($ticketId)
    {
        try {
            error_log("MessageController - getTicketMessages called with ticketId: $ticketId");

            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                return new JsonResponse(['error' => 'Ticket not found'], 404);
            }

            $messages = $ticket->getMessages();
            $messageData = [];
            foreach ($messages as $message) {
                $messageData[] = [
                    'id' => $message->getId(),
                    'author' => $message->getAuthor()->getFirstName() . ' ' . $message->getAuthor()->getLastName(),
                    'recipient' => $message->getRecipient()->getFirstName() . ' ' . $message->getRecipient()->getLastName(),
                    'content' => $message->getContent(),
                    'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse($messageData);
        } catch (\Exception $e) {
            error_log("Exception in getTicketMessages: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }
}
?>
