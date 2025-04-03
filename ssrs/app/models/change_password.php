<?php
session_start();
// Authentication check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../app/controllers/UserController.php";
$userController = new UserController();

$userId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$userId) {
    header("Location: admin_dashboard.php");
    exit;
}

// Get user data
$user = $userController->getUserById($userId);

if (!$user) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        if ($userController->changePassword($userId, $newPassword)) {
            $success = "Password changed successfully";
        } else {
            $error = "Failed to change password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Change Password</title>
  
</head>

<body>
    <h2>Smart Service Request System</h2>
    <h3>Change Password for <?php echo $user['name']; ?></h3>
    
    <div>
        <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
    </div>
    
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
            <button type="submit">Change Password</button>
        </div>
    </form>
</body>
</html>