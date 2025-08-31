<?php
// Include PHPMailer autoload.php file
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Emailsender {
    private $mail;

    public function __construct() {
        // Create a new PHPMailer instance
        $this->mail = new PHPMailer(true); // Passing true enables exceptions
        
        // Set up the SMTP configuration
        $this->mail->isSMTP();
        $this->mail->Host = 'mail.liveasoft.com'; // Your SMTP host
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'info@liveasoft.com'; // Your SMTP username
        $this->mail->Password = 'Ry7b9g{V56(l'; // Your SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption   
        $this->mail->Port = 465; // TCP port to connect to
        $this->mail->isHTML(true);
    }


    public function sendEmail($recipient, $subject, $body) {
        try {
            // Set up the email content
            $this->mail->setFrom('info@liveasoft.com', 'Support');
            $this->mail->addAddress($recipient);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            // Send the email
            $this->mail->send();
            return 1;
        } catch (Exception $e) {
            // echo 'Email could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
            return 0;
        }
    }

}

$emailsender = new Emailsender();


?>
