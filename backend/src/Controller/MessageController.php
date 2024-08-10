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
                    return new JsonResponse(['error' => 'Ticket ID not specified'], 400);
                case 'GET':
                    if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
                        return $this->getTicketMessages((int)$uriParts[1]);
                    }
                    return new JsonResponse(['error' => 'Ticket ID not specified'], 400);
                default:
                    return new JsonResponse(['error' => 'Method Not Allowed'], 405);
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

        public function addMessage($ticketId, $data)
    {
        try {
            error_log("MessageController - Data received for creating message: " . json_encode($data));

            // Vérifiez que l'utilisateur est connecté et a une session active
            $authorId = $this->getCurrentUserId();
            if (!$authorId) {
                return new JsonResponse(['error' => 'Unauthorized'], 401);
            }

            // Assurez-vous que le contenu du message est bien envoyé
            if (!isset($data['recipient_id']) || !isset($data['content'])) {
                return new JsonResponse(['error' => 'Missing required fields for new message'], 400);
            }

            // Récupération du ticket
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                return new JsonResponse(['error' => 'Ticket not found'], 404);
            }

            // Récupération de l'auteur et du destinataire
            $author = $this->entityManager->find(UserModel::class, $authorId);
            $recipient = $this->entityManager->find(UserModel::class, $data['recipient_id']);
            if (!$recipient) {
                return new JsonResponse(['error' => 'Recipient not found'], 404);
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

            return new JsonResponse([
                'message' => 'Message added successfully',
                'message_id' => $message->getId(),
                'ticket_id' => $ticket->getId(),
                'author_id' => $author->getId(),
                'recipient_id' => $recipient->getId(),
                'content' => $message->getContent(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s')
            ], 200);
        } catch (\Exception $e) {
            error_log("Exception in addMessage: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
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
            // Démarre la mise en tampon de sortie
            ob_start();
        
            error_log("Début de getTicketMessages pour le ticket ID: $ticketId");
        
            // Récupération du ticket
            $ticket = $this->entityManager->find(TicketModel::class, $ticketId);
            if (!$ticket) {
                error_log("Ticket non trouvé pour ID: $ticketId");
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Ticket not found']);
                ob_end_flush(); // Envoie le contenu du tampon et désactive le tampon de sortie
                exit;
            }
    
            // Récupération des messages
            $messages = $ticket->getMessages();
            if ($messages->isEmpty()) {
                error_log("Aucun message trouvé pour le ticket ID: $ticketId");
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Il n\'y a aucun message dans ce ticket.']);
                ob_end_flush(); // Envoie le contenu du tampon et désactive le tampon de sortie
                exit;
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
    
            // Vérification des données avant de renvoyer la réponse
            error_log("Final message data before sending: " . json_encode($messageData));
    
            header('Content-Type: application/json');
            echo json_encode($messageData);
            ob_end_flush(); // Envoie le contenu du tampon et désactive le tampon de sortie
            exit;
    
        } catch (\Exception $e) {
            // Nettoie et désactive le tampon de sortie en cas d'erreur
            if (ob_get_length()) {
                ob_end_clean();
            }
    
            error_log("Error in getTicketMessages: " . $e->getMessage());
    
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal Server Error']);
            exit;
        }
    }
}
?>