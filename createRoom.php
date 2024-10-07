<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['createRoom'])) {
        $roomName = $data['roomName'];
        $isPrivate = $data['isPrivate'] === true ? 0 : 1;
        $password = $isPrivate === 0 ? password_hash($data['password'], PASSWORD_DEFAULT) : NULL;
        $pin = $isPrivate === 0 ? $data['pin'] : NULL;
        $createdBy = $data['createdBy'];

        $stmt = $conn->prepare("INSERT INTO rooms (name, isPublic, password, pin, createdBy) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $roomName, $isPrivate, $password, $pin, $createdBy);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'room' => ['id' => $stmt->insert_id, 'name' => $roomName]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create room']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
