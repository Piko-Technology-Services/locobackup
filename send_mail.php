<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // adjust path if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    $supportEmail = "support@locobackup.space";
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $supportEmail;
        $mail->Password = 'Support@Loco123'; // replace with actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // --- Email to support ---
        $mail->setFrom($supportEmail, 'Loco Backup Website');
        $mail->addAddress($supportEmail);
        $mail->isHTML(true);
        $mail->Subject = "New Demo Request - Loco Email Backup";
        $mail->Body = "<h3>New Demo Request</h3><p>Email: <strong>{$userEmail}</strong></p><p>Submitted on " . date('Y-m-d H:i:s') . "</p>";
        $mail->send();

        // --- Auto reply to user ---
        $mail->clearAddresses();
        $mail->addAddress($userEmail);
        $mail->Subject = "Loco Email Backup Demo Request Received";
        $mail->Body = '
        <div style="font-family:Poppins,Arial,sans-serif;background-color:#f9f9f9;padding:30px;">
          <div style="max-width:600px;margin:auto;background:#fff;border-radius:10px;padding:25px;text-align:center;">
            <img src="https://locobackup.space/assets/images/logo/logo.png" alt="Loco Logo" style="width:70px;margin-bottom:10px;">
            <h2 style="color:#0d6efd;margin-bottom:10px;">Loco Email Backup</h2>
            <p style="color:#333;">Hi there 👋,</p>
            <p style="color:#555;">We’ve received your demo request. The installation package will be sent to your inbox shortly.</p>
            <p style="color:#999;font-size:12px;margin-top:20px;">This is an automated email — please do not reply.</p>
          </div>
        </div>';

        $mail->send();

        echo "<script>alert('Thank you! Your request has been received. Please check your email.'); window.location.href='download.html';</script>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
