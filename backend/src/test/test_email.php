<?php
require 'vendor/autoload.php';

use SendGrid\Mail\Mail;

function sendTestEmail($sendgridApiKey)
{
    $email = new Mail();
    $email->setFrom("morewaste1@gmail.com", "No More Waste");
    $email->setSubject("Test Email");
    $email->addTo("alexandreuzan9@gmail.com", "Volunteer");
    $email->addContent("text/plain", "This is a test email from No More Waste.");

    $sendgrid = new \SendGrid($sendgridApiKey);
    try {
        $response = $sendgrid->send($email);
        echo "Email sent successfully. Response code: " . $response->statusCode() . "\n";
        print_r($response->headers());
        echo $response->body() . "\n";
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage() . "\n";
    }
}

$sendgridApiKey = "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";
sendTestEmail($sendgridApiKey);
?>
