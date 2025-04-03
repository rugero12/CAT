<?php
session_start();

require_once "../vendor/autoload.php"; // Ensure this path is correct
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../app/controllers/UserController.php";
    $userController = new UserController();

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the email exists
    $user = $userController->getUserByEmail($email);
    if ($user) {
        // Generate a password reset token
        $resetToken = bin2hex(random_bytes(16));
        $userController->savePasswordResetToken($user['id'], $resetToken);

        // Send reset link to the user's email
        $resetLink = "http://localhost/ssrs/auth/reset_password.php?token=$resetToken";

        // Configure PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use Gmail's SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Your Gmail address
            $mail->Password = 'your-email-password'; // Your Gmail password or app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email settings
            $mail->setFrom('your-email@gmail.com', 'SSRS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link to reset your password: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $success = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Forgot Password</title>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Forgot Password</h3>
    
    <?php if (isset($success)): ?>
        <div style="color: green;"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div>
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <button type="submit">Send Reset Link</button>
        </div>
    </form>
</body>
</html>
