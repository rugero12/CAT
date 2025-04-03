<?php
session_start();
// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
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

// Prevent self-deletion or self-password change check
if ($userId == $_SESSION['user_id']) {
    $selfEdit = true;
} else {
    $selfEdit = false;
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
    <style>
        /* Global Styles */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(180deg, #f4f4f4, #e0e0e0);
    color: #333;
    margin: 0;
    padding: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Container */
.container {
    max-width: 400px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Headings */
h2, h3 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
}

/* Back Link */
a {
    color: #3498db;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    text-decoration: underline;
    color: #2980b9;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

label {
    font-weight: bold;
    display: block;
    margin: 10px 0 5px;
    text-align: left;
}

input[type="password"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 6px rgba(52, 152, 219, 0.3);
}

/* Success & Error Messages */
div[style*="green"] {
    background-color: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 6px;
    border: 2px solid #c3e6cb;
}

div[style*="red"] {
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 6px;
    border: 2px solid #f5c6cb;
}

/* Buttons */
button {
    background: #3498db;
    color: white;
    padding: 12px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: transform 0.2s ease, background 0.3s ease;
}

button:hover {
    background: #2980b9;
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        width: 90%;
        padding: 20px;
    }

    button {
        width: 100%;
    }
}
</style>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Change Password for <?php echo htmlspecialchars($user['name']); ?></h3>
    
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