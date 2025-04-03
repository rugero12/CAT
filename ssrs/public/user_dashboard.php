<?php
session_start();
// Authentication check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}
require_once "../app/controllers/RequestController.php";
$requestController = new RequestController();
$requests = $requestController->getUserRequests($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        h2, h3, h4 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        h2 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        h4 {
            margin-top: 25px;
            border-left: 4px solid #3498db;
            padding-left: 10px;
        }

        .welcome-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ecf0f1;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .welcome-message {
            font-weight: bold;
        }

        .create-request-box {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .create-button {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .create-button:hover {
            background-color: #27ae60;
            text-decoration: none;
        }

        .requests-container {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .actions a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s;
        }

        .actions a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .actions a.delete {
            color: #e74c3c;
        }

        .actions a.delete:hover {
            color: #c0392b;
        }

        .no-requests {
            padding: 20px;
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
        }

        .status-label {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .status-pending {
            background-color: #f1c40f;
            color: #fff;
        }

        .status-in-progress {
            background-color: #3498db;
            color: #fff;
        }

        .status-completed {
            background-color: #2ecc71;
            color: #fff;
        }

        .status-rejected {
            background-color: #e74c3c;
            color: #fff;
        }

        a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            table {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 8px 10px;
            }
            
            .actions a {
                display: block;
                margin: 5px 0;
            }
        }

        @media (max-width: 576px) {
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>User Dashboard</h3>
    
    <div class="welcome-bar">
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <a href="../auth/logout.php">Logout</a>
    </div>
    
    <div class="create-request-box">
        <h4 style="margin: 0;">Create New Service Request</h4>
        <a href="request_create.php" class="create-button">Create New Request</a>
    </div>
    
    <div class="requests-container">
        <h4>My Service Requests</h4>
        
        <?php if($requests->rowCount() > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
                
                <?php while($row = $requests->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td>
                            <?php 
                                $statusClass = '';
                                switch(strtolower($row['status'])) {
                                    case 'pending':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'in progress':
                                        $statusClass = 'status-in-progress';
                                        break;
                                    case 'completed':
                                        $statusClass = 'status-completed';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'status-rejected';
                                        break;
                                }
                            ?>
                            <span class="status-label <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="actions">
                            <a href="request_view.php?id=<?php echo $row['id']; ?>">View</a>
                            <a href="request_edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="request_delete.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this request?')" 
                               class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-requests">You don't have any service requests yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>