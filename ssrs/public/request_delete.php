<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../app/controllers/RequestController.php";
$requestController = new RequestController();

// Check for request ID
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$requestId = $_GET['id'];

// Delete the request
if ($requestController->deleteRequest($requestId)) {
    $_SESSION['success'] = "Request deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete request.";
}

header("Location: admin_dashboard.php");
exit;
?>