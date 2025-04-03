<?php
class Request {
    private $conn;
    private $table_name = "requests";

    public $id;
    public $user_id;
    public $title;
    public $description;
    public $category;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create request
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET user_id=:user_id, title=:title, description=:description, 
                    category=:category, status=:status";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all requests (admin)
    public function readAll() {
        $query = "SELECT r.*, u.name as user_name 
                 FROM " . $this->table_name . " r
                 LEFT JOIN users u ON r.user_id = u.id
                 ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read user requests
    public function readUserRequests($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Read single request
    public function readOne() {
        $query = "SELECT r.*, u.name as user_name 
                 FROM " . $this->table_name . " r
                 LEFT JOIN users u ON r.user_id = u.id
                 WHERE r.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->category = $row['category'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Update request
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET title=:title, description=:description, 
                    category=:category, status=:status
                WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete request
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>