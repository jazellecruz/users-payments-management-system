<?php 

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function createEmailClient($config) {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = EMAIL_SERVICE_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = EMAIL_SERVICE_SENDER;
    $mail->Password   = EMAIL_SERVICE_PASSWORD;
    $mail->Port       = EMAIL_SERVICE_PORT;                  // Set email format to HTML

    return $mail;
}

function sendEmail($emailData, $config =[]) {
    try {
        $mail = createEmailClient([]);

        // Recipients
        $mail->setFrom(EMAIL_SERVICE_SENDER, 'Journeolink');
        $mail->addAddress($emailData['to']);

        // Content
        $mail->isHTML($config['isHTML'] ?? false);
        $mail->Subject = $emailData['subject'];
        $mail->Body    = $emailData['body'];

        return $mail->send();

    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}