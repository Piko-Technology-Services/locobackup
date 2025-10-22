<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // adjust path if needed

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $userOS = isset($_POST['os']) ? htmlspecialchars($_POST['os']) : '';
    
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    
    if (empty($userOS)) {
        echo json_encode(['success' => false, 'message' => 'Please select your operating system.']);
        exit;
    }

    $supportEmail = "support@locobackup.space";
    $adminEmail = "bupe@pikozm.com";
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $supportEmail;
        $mail->Password = 'Support@Loco123'; // replace with actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // --- Email to admin ---
        $mail->setFrom($supportEmail, 'Loco Backup Website');
        $mail->addAddress($adminEmail);
        $mail->isHTML(true);
        $mail->Subject = "New Demo Request - Loco Email Backup";
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;'>
            <h2 style='color:#0d6efd;border-bottom:2px solid #0d6efd;padding-bottom:10px;'>New Demo Request</h2>
            <div style='background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;'>
                <p><strong>Email Address:</strong> {$userEmail}</p>
                <p><strong>Operating System:</strong> {$userOS}</p>
                <p><strong>Request Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            </div>
            <p style='color:#666;font-size:14px;'>This request was submitted through the Loco Email Backup website.</p>
        </div>";
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
            <p style="color:#555;">We\'ve received your demo request for <strong>' . $userOS . '</strong>. The installation package will be sent to your inbox shortly.</p>
            <p style="color:#999;font-size:12px;margin-top:20px;">This is an automated email — please do not reply.</p>
          </div>
        </div>';

        $mail->send();

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Demo request sent successfully!']);
        
    } catch (Exception $e) {
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $mail->ErrorInfo]);
    }
} else {
    // Return error for non-POST requests
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
