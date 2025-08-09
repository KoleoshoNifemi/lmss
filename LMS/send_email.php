<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server (e.g., smtp.gmail.com)
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'your-email@gmail.com';                 // SMTP username (your email address)
    $mail->Password   = 'your-email-password';                  // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
    $mail->Port       = 587;                                    // TCP port to connect to (587 for TLS)

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'Your Name');        // Sender email and name
    $mail->addAddress('recipient@example.com', 'Recipient Name'); // Recipient email and name

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a <b>test email</b> sent using PHPMailer.';
    $mail->AltBody = 'This is a plain text version of the email.';

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}
?>