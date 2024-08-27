<?php
namespace Controller;

use Entity\MessageModel;
use Entity\TicketModel;
use Entity\UserModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Service\EmailService;

class MessageController
{
    private $entityManager;
    private $emailService;

    public function __construct(EntityManager $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
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
                    $response = new JsonResponse(['error' => 'Ticket ID not specified'], 400);
                    $response->send();
                    exit();
                case 'GET':
                    if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
                        return $this->getTicketMessages((int)$uriParts[1]);
                    }
                    $response = new JsonResponse(['error' => 'Ticket ID not specified'], 400);
                    $response->send();
                    exit();
                default:
                    $response = new JsonResponse(['error' => 'Method Not Allowed'], 405);
                    $response->send();
                    exit();
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            $response = new JsonResponse(['error' => 'Internal Server Error'], 500);
            $response->send();
            exit();
        }
    }

    public function addMessage($ticketId, $data)
    {
        try {
            error_log("MessageController - Data received for creating message: " . json_encode($data));

            // Vérifiez que l'ID de l'utilisateur est présent dans les données d'entrée
            if (!isset($data['author_id']) || empty($data['author_id'])) {
                $response = new JsonResponse(['error' => 'Unauthorized'], 401);
                $response->send();
                exit();
            }

            // Assurez-vous que le contenu du message est bien envoyé
            if (!isset($data['recipient_id']) || !isset($data['content'])) {
                $response = new JsonResponse(['error' => 'Missing required fields for new message'], 400);
                $response->send();
                exit();
            }

            // Récupération du ticket
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                $response = new JsonResponse(['error' => 'Ticket not found'], 404);
                $response->send();
                exit();
            }

            // Récupération de l'auteur et du destinataire
            $author = $this->entityManager->find(UserModel::class, $data['author_id']);
            $recipient = $this->entityManager->find(UserModel::class, $data['recipient_id']);
            if (!$author || !$recipient) {
                $response = new JsonResponse(['error' => 'User not found'], 404);
                $response->send();
                exit();
            }

            // Validation que l'utilisateur qui envoie le message est bien autorisé (author ou recipient)
            if ($author->getId() !== $data['author_id'] && $recipient->getId() !== $data['author_id']) {
                $response = new JsonResponse(['error' => 'User not authorized to send a message on this ticket'], 403);
                $response->send();
                exit();
            }

            // Création du message
            $message = new MessageModel();
            $message->setTicket($ticket);
            $message->setAuthor($author);
            $message->setRecipient($recipient);
            $message->setContent($data['content']);
            $message->setCreatedAt(new \DateTime("now"));

            // Sauvegarde du message dans la base de données
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            // Envoi d'un email au destinataire
            $this->sendEmailNotification($recipient->getEmail(), $message);

            // Vérification avant d'envoyer la réponse
            $response = new JsonResponse([
                'message' => 'Message added successfully',
                'message_id' => $message->getId(),
                'ticket_id' => $ticket->getId(),
                'author_id' => $author->getId(),
                'recipient_id' => $recipient->getId(),
                'content' => $message->getContent(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s')
            ], 200);
            $response->send();
            exit();

        } catch (\Exception $e) {
            error_log("Exception in addMessage: " . $e->getMessage());
            $response = new JsonResponse(['error' => 'Internal Server Error'], 500);
            $response->send();
            exit();
        }
    }

    private function sendEmailNotification($email, $message)
    {
        $subject = "Nouveau message reçu";
        $body = "Vous avez reçu un nouveau message de " . $message->getAuthor()->getFirstName() . " " . $message->getAuthor()->getLastName() . ".\n\nContenu du message:\n\n" . $message->getContent();
        $this->emailService->sendEmail($email, $subject, $body);
    }

    public function getTicketMessages($ticketId)
    {
        try {
            error_log("Début de getTicketMessages pour le ticket ID: $ticketId");

            // Récupération du ticket
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                error_log("Ticket non trouvé pour ID: $ticketId");
                $response = new JsonResponse(['error' => 'Ticket not found'], 404);
                $response->send();
                exit();
            }

            // Récupération des messages
            $messages = $ticket->getMessages();
            if ($messages->isEmpty()) {
                error_log("Aucun message trouvé pour le ticket ID: $ticketId");
                $response = new JsonResponse(['message' => 'Il n\'y a aucun message dans ce ticket.'], 200);
                $response->send();
                exit();
            }

            $messageData = [];
            foreach ($messages as $message) {
                $messageData[] = [
                    'id' => $message->getId(),
                    'author' => $message->getAuthor() ? $message->getAuthor()->getFirstName() . ' ' . $message->getAuthor()->getLastName() : 'Unknown',
                    'recipient' => $message->getRecipient() ? $message->getRecipient()->getFirstName() . ' ' . $message->getRecipient()->getLastName() : 'Unknown',
                    'content' => $message->getContent(),
                    'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            error_log("Final message data before sending: " . json_encode($messageData));
            $response = new JsonResponse($messageData, 200);
            $response->send();
            exit();

        } catch (\Exception $e) {
            error_log("Error in getTicketMessages: " . $e->getMessage());
            $response = new JsonResponse(['error' => 'Internal Server Error'], 500);
            $response->send();
            exit();
        }
    }
}
?>
