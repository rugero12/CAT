<?php
session_start();
// Authentication check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}
// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../app/controllers/RequestController.php";
    
    $requestController = new RequestController();
    
    $requestData = array(
        "user_id" => $_SESSION['user_id'],
        "title" => $_POST['title'],
        "description" => $_POST['description'],
        "category" => $_POST['category']
    );
    
    if($requestController->createRequest($requestData)) {
        header("Location: user_dashboard.php");
        exit;
    } else {
        $create_error = "Failed to create request";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Create Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        h2, h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        h2 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }

        input[type="text"], 
        textarea, 
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, 
        textarea:focus, 
        select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        p {
            margin: 15px 0;
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

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            form {
                padding: 15px;
            }
            
            button[type="submit"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Create Service Request</h3>
    
    <?php if(isset($create_error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($create_error); ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required placeholder="Please provide details about your request..."></textarea>
        </div>
        <div>
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="IT Support">IT Support</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Administrative">Administrative</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div>
            <button type="submit">Submit Request</button>
        </div>
    </form>
    
    <p><a href="user_dashboard.php">&larr; Back to Dashboard</a></p>
</body>
</html>