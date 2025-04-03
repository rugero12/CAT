<?php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;
    
    /**
     * Constructor with DB connection
     * 
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get the table name
     * 
     * @return string Table name
     */
    public function getTableName() {
        return $this->table_name;
    }
    
    /**
     * Create new user
     * 
     * @return boolean Success or failure
     */
    public function create() {
        // Create query
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name = :name, 
                      email = :email, 
                      password = :password, 
                      role = :role, 
                      created_at = :created_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":created_at", $this->created_at);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email Email to check
     * @return boolean True if email exists, false otherwise
     */
    public function emailExists($email) {
        // Query to check if email exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $email = htmlspecialchars(strip_tags($email));
        
        // Bind parameters
        $stmt->bindParam(":email", $email);
        
        // Execute query
        $stmt->execute();
        
        // Get number of rows
        $num = $stmt->rowCount();
        
        // If email exists, return true
        if ($num > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Find user by email
     * 
     * @param string $email Email to find
     * @return array|boolean User data if found, false otherwise
     */
    public function findByEmail($email) {
        // Query to find user by email
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $email = htmlspecialchars(strip_tags($email));
        
        // Bind parameters
        $stmt->bindParam(":email", $email);
        
        // Execute query
        $stmt->execute();
        
        // Check if found
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id User ID to find
     * @return array|boolean User data if found, false otherwise
     */
    public function findById($id) {
        // Query to find user by ID
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $id = htmlspecialchars(strip_tags($id));
        
        // Bind parameters
        $stmt->bindParam(":id", $id);
        
        // Execute query
        $stmt->execute();
        
        // Check if found
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    /**
     * Delete a user by ID
     * 
     * @param int $id User ID
     * @return boolean True if deletion is successful, false otherwise
     */
    public function deleteById($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $id = htmlspecialchars(strip_tags($id));
        
        // Bind parameter
        $stmt->bindParam(":id", $id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Update a user's password
     * 
     * @param int $id User ID
     * @param string $hashedPassword New hashed password
     * @return boolean True if successful, false otherwise
     */
    public function updatePassword($id, $hashedPassword) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $id = htmlspecialchars(strip_tags($id));
        $hashedPassword = htmlspecialchars(strip_tags($hashedPassword));
        
        // Bind parameters
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":password", $hashedPassword);
        
        // Execute query
        return $stmt->execute();
    }

    /**
     * Save password reset token
     * 
     * @param int $userId User ID
     * @param string $token Reset token
     * @return boolean True if successful, false otherwise
     */
    public function saveResetToken($userId, $token) {
        $query = "UPDATE " . $this->table_name . " SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":id", $userId);
        return $stmt->execute();
    }

    /**
     * Check if reset token is valid
     * 
     * @param string $token Reset token
     * @return boolean True if valid, false otherwise
     */
    public function isValidResetToken($token) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE reset_token = :token AND reset_token_expiry > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Reset password using token
     * 
     * @param string $token Reset token
     * @param string $hashedPassword New hashed password
     * @return boolean True if successful, false otherwise
     */
    public function resetPasswordWithToken($token, $hashedPassword) {
        $query = "UPDATE " . $this->table_name . " SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":token", $token);
        return $stmt->execute();
    }

    /**
     * Update user details
     * 
     * @param array $userData Updated user data
     * @return boolean True if successful, false otherwise
     */
    public function updateDetails($userData) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, email = :email";
        
        if (!empty($userData['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $userData['name'] = htmlspecialchars(strip_tags($userData['name']));
        $userData['email'] = htmlspecialchars(strip_tags($userData['email']));
        $userData['id'] = htmlspecialchars(strip_tags($userData['id']));
        
        // Bind parameters
        $stmt->bindParam(":name", $userData['name']);
        $stmt->bindParam(":email", $userData['email']);
        $stmt->bindParam(":id", $userData['id']);
        
        if (!empty($userData['password'])) {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashedPassword);
        }
        
        // Execute query
        return $stmt->execute();
    }
}
?>