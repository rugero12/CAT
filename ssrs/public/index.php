<?php
session_start();

// Redirect to appropriate dashboard if logged in
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
} else {
    // Not logged in, redirect to login
    header("Location: ../auth/login.php");
    exit;
}
?>