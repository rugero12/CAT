<?php
class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "ssrs";
    private $username = "root";
    private $password = "";
    public $conn;
    
    /**
     * Get database connection
     * 
     * @return PDO Database connection
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>