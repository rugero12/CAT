<?php
require_once "../app/models/User.php";
require_once "../config/database.php";

class UserController {
    private $db;
    private $user;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }
    
    /**
     * Register a new user
     * 
     * @param array $userData User data including name, email, password, and role
     * @return boolean Success or failure
     */
    public function register($userData) {
        // Validate required fields
        if (empty($userData['name']) || empty($userData['email']) || 
            empty($userData['password']) || empty($userData['role'])) {
            return false;
        }
        
        // Check if email already exists
        if ($this->user->emailExists($userData['email'])) {
            return false;
        }
        
        // Set user properties
        $this->user->name = $userData['name'];
        $this->user->email = $userData['email'];
        $this->user->password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $this->user->role = $userData['role'];
        $this->user->created_at = date('Y-m-d H:i:s');
        
        // Create the user
        if ($this->user->create()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Login a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array|boolean User data if login successful, false otherwise
     */
    public function login($email, $password) {
        // Validate required fields
        if (empty($email) || empty($password)) {
            return false;
        }
        
        // Check if user exists
        $user = $this->user->findByEmail($email);
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Remove password from array
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|boolean User data if found, false otherwise
     */
    public function getUserById($id) {
        $user = $this->user->findById($id);
        if ($user) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
    
    /**
     * Get user by email
     * 
     * @param string $email User email
     * @return array|boolean User data if found, false otherwise
     */
    public function getUserByEmail($email) {
        return $this->user->findByEmail($email);
    }
    
    /**
     * Get all users
     * 
     * @return PDOStatement|boolean PDOStatement if successful, false otherwise
     */
    public function getAllUsers() {
        $query = "SELECT id, name, email, role, created_at FROM " . $this->user->getTableName() . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        
        if ($stmt->execute()) {
            return $stmt;
        }
        
        return false;
    }
    
    /**
     * Delete a user by ID
     * 
     * @param int $id User ID
     * @return boolean True if deletion is successful, false otherwise
     */
    public function deleteUser($id) {
        return $this->user->deleteById($id);
    }
    
    /**
     * Change a user's password
     * 
     * @param int $id User ID
     * @param string $newPassword New password
     * @return boolean True if successful, false otherwise
     */
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->user->updatePassword($id, $hashedPassword);
    }
    
    /**
     * Save password reset token
     * 
     * @param int $userId User ID
     * @param string $token Reset token
     * @return boolean True if successful, false otherwise
     */
    public function savePasswordResetToken($userId, $token) {
        return $this->user->saveResetToken($userId, $token);
    }
    
    /**
     * Check if reset token is valid
     * 
     * @param string $token Reset token
     * @return boolean True if valid, false otherwise
     */
    public function isValidResetToken($token) {
        return $this->user->isValidResetToken($token);
    }
    
    /**
     * Reset password using token
     * 
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return boolean True if successful, false otherwise
     */
    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->user->resetPasswordWithToken($token, $hashedPassword);
    }
    
    /**
     * Update user details
     * 
     * @param array $userData Updated user data
     * @return boolean True if successful, false otherwise
     */
    public function updateUser($userData) {
        return $this->user->updateDetails($userData);
    }
}
?>