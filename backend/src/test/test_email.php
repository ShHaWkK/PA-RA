<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendTestEmail()
{
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'morewaste1@gmail.com';
        $mail->Password = 'vhpewmlkxxrpnioj '; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataire
        $mail->setFrom('morewaste1@gmail.com', 'No More Waste');
        $mail->addAddress('alexandreuzan9@gmail.com', 'Volunteer');

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Test Email';
        $mail->Body    = 'This is a test email from No More Waste.';
        $mail->AltBody = 'This is a test email from No More Waste.';

        $mail->send();
        echo 'Email sent successfully';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

sendTestEmail();
