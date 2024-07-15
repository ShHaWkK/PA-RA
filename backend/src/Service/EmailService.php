<?php
// Path: backend/src/Service/EmailService.php
namespace Service;

use Entity\DeliveryModel;
use SendGrid\Mail\Mail;

class EmailService
{
    private $sendgridApiKey;

    public function __construct($sendgridApiKey)
    {
        $this->sendgridApiKey = $sendgridApiKey;
    }

    public function sendRoutePlan(DeliveryModel $delivery)
    {
        $to = "alexandreuzan9@gmail.com"; // Adresse e-mail du bénévole
        $subject = "Plan de route pour la livraison";
        $message = "Voici le plan de route pour votre livraison :\n";
        $message .= "Route : " . $delivery->getRouteName() . "\n";
        $message .= "Destination : " . $delivery->getDestination() . "\n";
        $message .= "Type de destinataire : " . $delivery->getRecipientType() . "\n";
        $message .= "Date de livraison : " . $delivery->getDeliveryDate()->format('Y-m-d H:i:s') . "\n";
        $message .= "Commentaire : " . $delivery->getComment() . "\n";

        // Utiliser Google Maps API pour ajouter un lien vers la carte
        $mapsLink = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($delivery->getDestination());
        $message .= "Plan de route : " . $mapsLink . "\n";

        // Création du mail
        $email = new Mail();
        $email->setFrom("morewaste1@gmail.com", "No More Waste");
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/plain", $message);

        // Envoi du mail via SendGrid
        $sendgrid = new \SendGrid($this->sendgridApiKey);
        try {
            $response = $sendgrid->send($email);
            error_log("Email sent successfully. Response code: " . $response->statusCode());
        } catch (\Exception $e) {
            error_log("Error sending email: " . $e->getMessage());
        }
    }
}
?>
