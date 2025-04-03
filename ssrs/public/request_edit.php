<?php
session_start();

// Authentication check
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check for request ID
if(!isset($_GET['id'])) {
    header("Location: " . ($_SESSION['user_role'] == 'admin' ? "admin_dashboard.php" : "user_dashboard.php"));
    exit;
}

require_once "../app/controllers/RequestController.php";
$requestController = new RequestController();

$request = $requestController->getRequest($_GET['id']);

// Ensure user can only edit their own requests unless admin
if (!$request || ($_SESSION['user_role'] != 'admin' && $request->user_id != $_SESSION['user_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestData = array(
        "id" => $request->id,
        "title" => $_POST['title'],
        "description" => $_POST['description'],
        "category" => $_POST['category'],
        "status" => isset($_POST['status']) ? $_POST['status'] : $request->status
    );
    
    if($requestController->updateRequest($requestData)) {
        header("Location: " . ($_SESSION['user_role'] == 'admin' ? "admin_dashboard.php" : "user_dashboard.php"));
        exit;
    } else {
        $edit_error = "Failed to update request";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Edit Request</title>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Edit Service Request</h3>
    
    <?php if(isset($edit_error)): ?>
        <p style="color: red;"><?php echo $edit_error; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        <div>
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo $request->title; ?>" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required><?php echo $request->description; ?></textarea>
        </div>
        <div>
            <label>Category:</label>
            <select name="category" required>
                <option value="IT Support" <?php echo $request->category == 'IT Support' ? 'selected' : ''; ?>>IT Support</option>
                <option value="Maintenance" <?php echo $request->category == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                <option value="Administrative" <?php echo $request->category == 'Administrative' ? 'selected' : ''; ?>>Administrative</option>
                <option value="Other" <?php echo $request->category == 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <?php if($_SESSION['user_role'] == 'admin'): ?>
        <div>
            <label>Status:</label>
            <select name="status">
                <option value="pending" <?php echo $request->status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in-progress" <?php echo $request->status == 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="resolved" <?php echo $request->status == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        <?php endif; ?>
        
        <div>
            <button type="submit">Update Request</button>
        </div>
    </form>
    
    <p><a href="<?php echo $_SESSION['user_role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">Back to Dashboard</a></p>
</body>
</html>