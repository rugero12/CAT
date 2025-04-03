<?php
// Define absolute paths to avoid include issues
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/ssrs';

// Include required files
require_once $root_path . "/config/database.php";
require_once $root_path . "/app/models/Request.php";

// Define Response class if it's not already available
class Response {
    private $conn;
    public $id;
    public $request_id;
    public $admin_id;
    public $response_message;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO responses (request_id, admin_id, response_message) 
                  VALUES (:request_id, :admin_id, :response_message)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":request_id", $this->request_id);
        $stmt->bindParam(":admin_id", $this->admin_id);
        $stmt->bindParam(":response_message", $this->response_message);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function readByRequest($request_id) {
        // Basic query without ORDER BY to avoid column issues
        $query = "SELECT * FROM responses WHERE request_id = :request_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":request_id", $request_id);
        $stmt->execute();
        
        return $stmt;
    }
}

// Define the RequestController class
class RequestController {
    private $db;
    private $request;
    private $response;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->request = new Request($this->db);
        $this->response = new Response($this->db);
    }
    
    public function createRequest($requestData) {
        $this->request->user_id = $requestData['user_id'];
        $this->request->title = $requestData['title'];
        $this->request->description = $requestData['description'];
        $this->request->category = $requestData['category'];
        $this->request->status = 'pending';
        
        if($this->request->create()) {
            return true;
        }
        return false;
    }
    
    public function getUserRequests($user_id) {
        return $this->request->readUserRequests($user_id);
    }
    
    public function getAllRequests() {
        return $this->request->readAll();
    }
    
    public function getRequest($id) {
        $this->request->id = $id;
        if($this->request->readOne()) {
            return $this->request;
        }
        return false;
    }
    
    public function updateRequest($requestData) {
        $this->request->id = $requestData['id'];
        $this->request->title = $requestData['title'];
        $this->request->description = $requestData['description'];
        $this->request->category = $requestData['category'];
        $this->request->status = $requestData['status'];
        
        if($this->request->update()) {
            return true;
        }
        return false;
    }
    
    public function deleteRequest($id) {
        $this->request->id = $id;
        if($this->request->delete()) {
            return true;
        }
        return false;
    }
    
    public function addResponse($responseData) {
        $this->response->request_id = $responseData['request_id'];
        $this->response->admin_id = $responseData['admin_id'];
        $this->response->response_message = $responseData['response_message'];
        
        if($this->response->create()) {
            return true;
        }
        return false;
    }
    
    public function getResponses($request_id) {
        return $this->response->readByRequest($request_id);
    }
}
?>