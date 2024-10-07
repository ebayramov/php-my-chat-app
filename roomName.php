<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$servername = "autorack.proxy.rlwy.net";  
$username = "root";                       
$password = "yqZxCOaIxIBOsRHzXhZxWYyesSgsCwyj"; 
$dbname = "railway";                      
$port = 55455;                         

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $roomId = $_GET['id'];
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT name, isPublic, password FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['name' => $row['name'], 'password' => $row['password'], 'isPublic' => $row['isPublic']]);
        } else {
            echo json_encode(['error' => 'Room not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Room ID not provided']);
    }
}

$conn->close();
?>
