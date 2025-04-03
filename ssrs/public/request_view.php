<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the RequestController class with absolute path
require_once $_SERVER['DOCUMENT_ROOT'] . '/ssrs/app/controllers/requestcontroller.php';

// Check if request ID is provided
if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$request_id = $_GET['id'];

// Create an instance of the RequestController
$requestController = new RequestController();

// Get request details
$request = $requestController->getRequest($request_id);

// Check if the request exists
if (!$request) {
    header("Location: user_dashboard.php");
    exit();
}

// Get responses for this request
$responses = $requestController->getResponses($request_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Details</title>
    <!-- Include your CSS files here -->
</head>
<body>
    <!-- Your HTML code here -->
    <h1>Request Details</h1>
    
    <!-- Display request information -->
    <div class="request-details">
        <h2><?php echo htmlspecialchars($request->title); ?></h2>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($request->category); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($request->status); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($request->description); ?></p>
    </div>
    
    <!-- Display responses -->
    <h3>Responses</h3>
    <div class="responses">
        <?php
        $hasResponses = false;
        if ($responses && $responses->rowCount() > 0) {
            $hasResponses = true;
            while ($row = $responses->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="response">';
                echo '<p><strong>Admin:</strong> ' . htmlspecialchars($row['admin_id']) . '</p>';
                echo '<p>' . htmlspecialchars($row['response_message']) . '</p>';
                echo '</div>';
            }
        }
        
        if (!$hasResponses) {
            echo '<p>No responses yet.</p>';
        }
        ?>
    </div>
    
    <!-- Add more HTML as needed -->
</body>
</html>