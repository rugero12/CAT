<?php
session_start();
// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../app/controllers/UserController.php";
$userController = new UserController();

// Check for user ID
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$userId = $_GET['id'];

// Prevent self-deletion
if ($userId == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header("Location: admin_dashboard.php");
    exit;
}

// Delete the user
if ($userController->deleteUser($userId)) {
    $_SESSION['success'] = "User deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete user.";
}

header("Location: admin_dashboard.php");
exit;
?>