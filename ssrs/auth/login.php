<?php
session_start();

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../app/controllers/UserController.php"; // Include the UserController class
    
    $userController = new UserController();
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email input
    $password = $_POST['password']; // Get password input
    
    $user = $userController->login($email, $password); // Attempt login
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: ../public/admin_dashboard.php");
        } else {
            header("Location: ../public/user_dashboard.php");
        }
        exit;
    } else {
        $login_error = "Invalid email or password"; // Error message if login fails
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Login</title>
    <style>
        /* General Styling */
body {
    background-color:rgb (88, 117, 126);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Form Container */
.container {
    width: 350px;
    padding: 25px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Header */
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Input Fields */
input[type="email"], 
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 2px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

input:focus {
    border-color: #008CBA;
    outline: none;
    box-shadow: 0 0 6px rgba(0, 140, 186, 0.3);
}

/* Show Password Checkbox */
input[type="checkbox"] {
    margin: 10px 0;
}

/* Button */
button {
    width: 100%;
    padding: 12px;
    background-color: #007BFF;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

/* Links */
.links {
    text-align: center;
    margin-top: 15px;
}

.links a {
    color: #007BFF;
    text-decoration: none;
    font-weight: 500;
}

.links a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Smart Service Request System</h2>
        <h3>Login</h3>
        
        <?php if(isset($login_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        
        <p>Don't have an account? <a href="../public/register.php">Register here</a></p>
        <div class="reset-password">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
