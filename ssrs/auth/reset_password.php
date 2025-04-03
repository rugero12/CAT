<?php
session_start();

require_once "../app/controllers/UserController.php";
$userController = new UserController();

$token = isset($_GET['token']) ? $_GET['token'] : null;

if (!$token || !$userController->isValidResetToken($token)) {
    die("Invalid or expired reset token.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        if ($userController->resetPassword($token, $newPassword)) {
            $success = "Password reset successfully. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Failed to reset password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Reset Password</title>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Reset Password</h3>
    
    <?php if (isset($success)): ?>
        <div style="color: green;"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div>
            <button type="submit">Reset Password</button>
        </div>
    </form>
</body>
</html>
