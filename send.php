<?php
header('Content-Type: application/json');

// ============================================================
// MANUAL INCLUDE (no Composer needed)
// ============================================================
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';
require __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUser = 'mildred.nunal@gmail.com';     
$smtpPass = 'vnmp scyd ltkp pdha';  
$fromEmail = $smtpUser;
$fromName  = 'Simple Mailer';

// ============================================================
// GET DATA FROM FRONTEND
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$to      = trim($input['to'] ?? '');
$cc      = trim($input['cc'] ?? '');
$bcc     = trim($input['bcc'] ?? '');
$subject = trim($input['subject'] ?? '(no subject)');
$message = trim($input['message'] ?? '');

if (empty($to)) {
    echo json_encode(['success' => false, 'error' => 'Recipient email is required']);
    exit;
}

// ============================================================
// SEND EMAIL
// ============================================================
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtpPort;
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($to);
    if (!empty($cc))  $mail->addCC($cc);
    if (!empty($bcc)) $mail->addBCC($bcc);

    // Content (plain text)
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body    = $message ?: '(empty message)';

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
?>