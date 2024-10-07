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
    $roomsResult = $conn->query("SELECT * FROM rooms");

    $rooms = [];
    while ($row = $roomsResult->fetch_assoc()) {
        $rooms[] = $row;
    }

    echo json_encode(['rooms' => $rooms]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $roomId = $data['roomId'];
    $password = $data['password'];

    // Fetch the room details
    $stmt = $conn->prepare("SELECT password FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        // Password is correct, allow the user to join the room
        echo json_encode(['status' => 'success', 'roomId' => $roomId]);
    } else {
        // Password is incorrect
        echo json_encode(['status' => 'error', 'message' => 'Password is not correct']);
    }
    $stmt->close();
   }

$conn->close();
