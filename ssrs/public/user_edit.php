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

// Get user data
$user = $userController->getUserById($userId);

if (!$user) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'id' => $userId,
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'password' => $_POST['password'] ?? null, // Optional password update
    ];

    if ($userController->updateUser($userData)) {
        $_SESSION['success'] = "User updated successfully.";
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Failed to update user.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (optional):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <button type="submit">Update User</button>
            </div>
        </form>

        <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
    </div>
</body>
</html>