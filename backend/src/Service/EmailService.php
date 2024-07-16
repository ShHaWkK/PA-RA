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
}
