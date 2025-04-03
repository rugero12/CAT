<?php
session_start();
// Already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
// Process registration form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../app/controllers/UserController.php";
    
    $userController = new UserController();
    
    // Validate password match
    if($_POST['password'] != $_POST['confirm_password']) {
        $register_error = "Passwords do not match";
    } else {
        $userData = array(
            "name" => $_POST['name'],
            "email" => $_POST['email'],
            "password" => $_POST['password'],
            "role" => $_POST['role'] // This will be passed to the controller
        );
        
        if($userController->register($userData)) {
            $register_success = "Registration successful. You can now login.";
        } else {
            $register_error = "Registration failed. Email may already be in use.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], 
        input[type="email"], 
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Register</h3>
    
    <?php if(isset($register_error)): ?>
        <div class="message error"><?php echo $register_error; ?></div>
    <?php endif; ?>
    
    <?php if(isset($register_success)): ?>
        <div class="message success"><?php echo $register_success; ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label>Register as:</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Administrator</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>
    
    <p>Already have an account? <a href="../auth/login.php">Login here</a></p>
</body>
</html>