<?php
// Path: backend/src/Service/EmailService.php
namespace Service;

use SendGrid\Mail\Mail;

class EmailService
{
    private $sendGrid;

    public function __construct($apiKey)
    {
        $this->sendGrid = new \SendGrid($apiKey);
    }

    public function sendEmail($to, $subject, $content)
    {
        $email = new Mail();
        $email->setFrom("no-reply@nomorewaste.com", "No More Waste");
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/plain", $content);

        try {
            $response = $this->sendGrid->send($email);
            return $response;
        } catch (Exception $e) {
            error_log('Caught exception: '. $e->getMessage());
            return false;
        }
    }
}
?>