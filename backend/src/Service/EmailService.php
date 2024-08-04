<?php
// Path: backend/src/Service/EmailService.php
namespace Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendVerificationEmail($email, $verificationCode)
    {
        try {
            $this->mailer->setFrom('morewaste1@gmail.com', 'No More Waste');
            $this->mailer->addAddress($email);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Verification Code';
            $this->mailer->Body    = 'Your verification code is: ' . $verificationCode;

            $this->mailer->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
            $this->mailer->setFrom('morewaste1@gmail.com', 'No More Waste');
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true); 
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }


    public function sendEmailChangeConfirmation($email) {
        try {
            $this->mailer->setFrom('morewaste1@gmail.com', 'No More Waste');
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Changement d\'email confirmé';
            $this->mailer->Body = 'Votre adresse email a été changée avec succès.';
            $this->mailer->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }

    public function sendApprovalEmail($email) {
        try {
            $this->mailer->setFrom('morewaste1@gmail.com', 'No More Waste');
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Votre inscription a été approuvée';
            $this->mailer->Body = 'Votre inscription a été approuvée. Bienvenue !';
            $this->mailer->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }
    public function sendRejectionEmail($email) {
        try {
            $this->mailer->setFrom('morewaste1@gmail.com', 'No More Waste');
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Votre inscription a été refusée';
            $this->mailer->Body = "Votre inscription a été refusée. Merci de votre compréhension.\n\n" .
                                  "Nous avons détecté une activité suspecte liée à des bots ou une tentative d'usurpation d'identité.\n\n" .
                                  "Conformément à l'article 226-4-1 du Code pénal français, l'usurpation d'identité est punie d'un an d'emprisonnement et de 15 000 euros d'amende.\n\n" .
                                  "Si vous pensez qu'il s'agit d'une erreur, veuillez nous contacter immédiatement.";
            $this->mailer->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }
}
