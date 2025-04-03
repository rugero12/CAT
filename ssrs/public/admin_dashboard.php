<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../app/controllers/RequestController.php";
require_once "../app/controllers/UserController.php";

$requestController = new RequestController();
$userController = new UserController();

$requests = $requestController->getAllRequests(); // Now returns PDOStatement
$users = $userController->getAllUsers(); // Get all users from the database
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRS - Admin Dashboard</title>
   <style type="text/css">
 /* Modern Minimalist Design System */
    :root {
      /* Color Palette - Calm & Professional */
      --primary: #4361ee;
      --primary-light: #4895ef;
      --primary-dark: #3a0ca3;
      --accent: #f72585;
      --success: #4cc9f0;
      --warning: #ffbe0b;
      --danger: #f94144;
      --gray-100: #f8f9fa;
      --gray-200: #e9ecef;
      --gray-300: #dee2e6;
      --gray-400: #ced4da;
      --gray-500: #adb5bd;
      --gray-600: #6c757d;
      --gray-700: #495057;
      --gray-800: #343a40;
      --gray-900: #212529;
      --white: #ffffff;
      
      /* Spacing & Layout */
      --spacing-xs: 0.25rem;
      --spacing-sm: 0.5rem;
      --spacing-md: 1rem;
      --spacing-lg: 1.5rem;
      --spacing-xl: 2rem;
      --spacing-xxl: 3rem;
      
      /* Typography */
      --font-family: 'Inter', system-ui, -apple-system, sans-serif;
      --font-size-base: 1rem;
      --font-size-sm: 0.875rem;
      --font-size-lg: 1.125rem;
      --font-size-xl: 1.25rem;
      --font-size-xxl: 1.5rem;
      --font-size-xxxl: 2rem;
      --font-weight-normal: 400;
      --font-weight-medium: 500;
      --font-weight-bold: 700;
      
      /* Effects */
      --border-radius-sm: 4px;
      --border-radius: 8px;
      --border-radius-lg: 12px;
      --border-radius-xl: 16px;
      --box-shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
      --box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      --box-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
      --transition-fast: 150ms ease;
      --transition: 250ms ease;
      --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Reset & Base Styles */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      font-size: 16px;
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--font-family);
      font-size: var(--font-size-base);
      line-height: 1.6;
      color: var(--gray-800);
      background-color: var(--gray-100);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      padding: var(--spacing-lg);
    }

    /* Layout */
    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: var(--spacing-lg);
      background-color: var(--white);
      border-radius: var(--border-radius-lg);
      box-shadow: var(--box-shadow);
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -15px;
    }

    .col {
      flex: 1;
      padding: 0 15px;
    }

    /* Typography */
    h1, h2, h3, h4, h5, h6 {
      margin-bottom: var(--spacing-md);
      font-weight: var(--font-weight-bold);
      line-height: 1.3;
      color: var(--gray-900);
    }

    h1 {
      font-size: var(--font-size-xxxl);
    }

    h2 {
      font-size: var(--font-size-xxl);
      position: relative;
      padding-bottom: var(--spacing-md);
    }

    h2::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      height: 3px;
      width: 60px;
      background-color: var(--primary);
      transition: var(--transition);
    }

    h2:hover::after {
      width: 100px;
    }

    h3 {
      font-size: var(--font-size-xl);
    }

    h4 {
      font-size: var(--font-size-lg);
    }

    p {
      margin-bottom: var(--spacing-md);
    }

    /* Tables */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: var(--spacing-lg) 0;
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: var(--box-shadow-sm);
    }

    thead {
      background-color: var(--primary);
      color: var(--white);
    }

    th {
      text-align: left;
      padding: var(--spacing-md);
      font-weight: var(--font-weight-medium);
      text-transform: uppercase;
      font-size: var(--font-size-sm);
      cursor: pointer;
    }

    th:hover {
      background-color: var(--primary-dark);
    }

    td {
      padding: var(--spacing-md);
      border-bottom: 1px solid var(--gray-200);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:nth-child(even) {
      background-color: var(--gray-100);
    }

    tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }

    /* Buttons */
    .btn {
      display: inline-block;
      padding: 0.6rem 1.2rem;
      font-size: var(--font-size-base);
      font-weight: var(--font-weight-medium);
      text-align: center;
      text-decoration: none;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      background-color: var(--primary);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--box-shadow);
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn::after {
      content: '';
      position: absolute;
      height: 100px;
      width: 100px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      transform: scale(0);
      opacity: 0;
      transition: transform 0.5s, opacity 0.3s;
    }

    .btn.ripple::after {
      animation: ripple 0.6s linear;
    }

    @keyframes ripple {
      0% {
        transform: scale(0);
        opacity: 0.5;
      }
      100% {
        transform: scale(4);
        opacity: 0;
      }
    }

    .btn-secondary {
      background-color: var(--gray-200);
      color: var(--gray-800);
    }

    .btn-secondary:hover {
      background-color: var(--gray-300);
    }

    .btn-success {
      background-color: var(--success);
    }

    .btn-warning {
      background-color: var(--warning);
      color: var(--gray-900);
    }

    .btn-danger {
      background-color: var(--danger);
    }

    /* Cards */
    .card {
      background-color: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow-sm);
      margin-bottom: var(--spacing-lg);
      overflow: hidden;
      transition: var(--transition);
    }

    .card:hover {
      box-shadow: var(--box-shadow-lg);
      transform: translateY(-4px);
    }

    .card-header {
      padding: var(--spacing-md);
      background-color: var(--gray-100);
      border-bottom: 1px solid var(--gray-200);
      position: relative;
    }

    .card-toggle {
      position: absolute;
      right: var(--spacing-md);
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      transition: var(--transition);
    }

    .card-toggle::before {
      content: '▼';
      font-size: 0.8rem;
    }

    .card-toggle.collapsed::before {
      content: '►';
    }

    .card-body {
      padding: var(--spacing-md);
      max-height: 500px;
      overflow: hidden;
      transition: max-height 0.5s ease;
    }

    .card-body.collapsed {
      max-height: 0;
      padding: 0 var(--spacing-md);
    }

    .card-title {
      font-size: var(--font-size-lg);
      margin-bottom: var(--spacing-sm);
    }

    /* Card Grid */
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: var(--spacing-lg);
      margin: var(--spacing-lg) 0;
    }

    /* Forms */
    .form-group {
      margin-bottom: var(--spacing-lg);
      position: relative;
    }

    .form-label {
      display: block;
      margin-bottom: var(--spacing-sm);
      font-weight: var(--font-weight-medium);
      color: var(--gray-700);
      transition: var(--transition);
    }

    .form-control {
      display: block;
      width: 100%;
      padding: 0.6rem 0.8rem;
      font-size: var(--font-size-base);
      line-height: 1.5;
      color: var(--gray-800);
      background-color: var(--white);
      border: 1px solid var(--gray-300);
      border-radius: var(--border-radius);
      transition: var(--transition-fast);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

    .form-floating {
      position: relative;
    }

    .form-floating .form-control {
      height: calc(3.5rem + 2px);
      padding: 1rem 0.8rem;
    }

    .form-floating > .form-label {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      padding: 1rem 0.8rem;
      pointer-events: none;
      border: 1px solid transparent;
      transform-origin: 0 0;
      transition: opacity .1s ease-in-out, transform .1s ease-in-out;
      color: var(--gray-500);
    }

    .form-floating > .form-control:focus ~ .form-label,
    .form-floating > .form-control:not(:placeholder-shown) ~ .form-label {
      opacity: .65;
      transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
      color: var(--primary);
    }

    /* Switch Toggle */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--gray-300);
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--primary);
    }

    input:focus + .slider {
      box-shadow: 0 0 1px var(--primary);
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Utilities */
    .text-center {
      text-align: center;
    }

    .mt-0 { margin-top: 0; }
    .mt-1 { margin-top: var(--spacing-xs); }
    .mt-2 { margin-top: var(--spacing-sm); }
    .mt-3 { margin-top: var(--spacing-md); }
    .mt-4 { margin-top: var(--spacing-lg); }
    .mt-5 { margin-top: var(--spacing-xl); }

    .mb-0 { margin-bottom: 0; }
    .mb-1 { margin-bottom: var(--spacing-xs); }
    .mb-2 { margin-bottom: var(--spacing-sm); }
    .mb-3 { margin-bottom: var(--spacing-md); }
    .mb-4 { margin-bottom: var(--spacing-lg); }
    .mb-5 { margin-bottom: var(--spacing-xl); }

    .p-0 { padding: 0; }
    .p-1 { padding: var(--spacing-xs); }
    .p-2 { padding: var(--spacing-sm); }
    .p-3 { padding: var(--spacing-md); }
    .p-4 { padding: var(--spacing-lg); }
    .p-5 { padding: var(--spacing-xl); }

    .d-flex { display: flex; }
    .flex-wrap { flex-wrap: wrap; }
    .justify-content-between { justify-content: space-between; }
    .justify-content-center { justify-content: center; }
    .align-items-center { align-items: center; }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.show {
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 1;
    }

    .modal-content {
      background-color: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow-lg);
      width: 90%;
      max-width: 500px;
      transform: translateY(-20px);
      transition: transform 0.3s ease;
    }

    .modal.show .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      padding: var(--spacing-md);
      border-bottom: 1px solid var(--gray-200);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title {
      margin: 0;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--gray-600);
    }

    .modal-body {
      padding: var(--spacing-md);
    }

    .modal-footer {
      padding: var(--spacing-md);
      border-top: 1px solid var(--gray-200);
      display: flex;
      justify-content: flex-end;
      gap: var(--spacing-sm);
    }

    /* Toast Notifications */
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1001;
    }

    .toast {
      background-color: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow-lg);
      padding: var(--spacing-md);
      margin-top: var(--spacing-md);
      min-width: 250px;
      max-width: 350px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transform: translateX(120%);
      transition: transform 0.3s ease;
    }

    .toast.show {
      transform: translateX(0);
    }

    .toast-primary { border-left: 4px solid var(--primary); }
    .toast-success { border-left: 4px solid var(--success); }
    .toast-warning { border-left: 4px solid var(--warning); }
    .toast-danger { border-left: 4px solid var(--danger); }

    .toast-close {
      background: none;
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
      color: var(--gray-600);
    }

    /* Progress */
    .progress {
      height: 0.75rem;
      background-color: var(--gray-200);
      border-radius: var(--border-radius-xl);
      overflow: hidden;
      margin: var(--spacing-md) 0;
    }

    .progress-bar {
      height: 100%;
      background-color: var(--primary);
      width: 0;
      transition: width 0.6s ease;
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.5s ease forwards;
    }

    /* Theme Toggle */
    .theme-dark {
      --primary: #6c5ce7;
      --primary-light: #a29bfe;
      --primary-dark: #5341d6;
      --accent: #e84393;
      --gray-100: #1a1a2e;
      --gray-200: #16213e;
      --gray-300: #0f3460;
      --gray-400: #406882;
      --gray-500: #6998ab;
      --gray-600: #b1d0e0;
      --gray-700: #d8e3e7;
      --gray-800: #edf2f7;
      --gray-900: #ffffff;
      --white: #121212;
      color: var(--gray-800);
    }

    /* Responsive Utilities */
    @media (max-width: 992px) {
      .container {
        max-width: 100%;
      }
    }

    @media (max-width: 768px) {
      .container {
        padding: var(--spacing-md);
      }
      
      .card-grid {
        grid-template-columns: 1fr;
      }
      
      .row {
        flex-direction: column;
      }
      
      .col {
        margin-bottom: var(--spacing-md);
      }
      
      h1 {
        font-size: calc(var(--font-size-xxxl) * 0.8);
      }
      
      h2 {
        font-size: calc(var(--font-size-xxl) * 0.8);
      }
      
      table {
        display: block;
        overflow-x: auto;
      }
    }
   </style>
</head>
<body>
    <h2>Smart Service Request System</h2>
    <h3>Admin Dashboard</h3>
    
    <div>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Admin) | 
           <a href="../auth/logout.php">Logout</a>
        </p>
    </div>
    
    <div>
        <h4>All Service Requests</h4>
        
        <?php if ($requests->rowCount() > 0): ?>
            <table>
                <tr>
                    <th>User</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $requests->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="request_view.php?id=<?php echo $row['id']; ?>">View</a> | 
                            <a href="request_edit.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="request_delete.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No requests found.</p>
        <?php endif; ?>
    </div>
    
    <div>
        <h4>All Users</h4>
        
        <?php if ($users->rowCount() > 0): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="change_password.php?id=<?php echo $row['id']; ?>">Change Password</a> | 
                            <a href="user_edit.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="user_delete.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
