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

$data = json_decode(file_get_contents('php://input'), true);

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['invitedUser'])) {
    $roomId = $data['roomId'];
    $roomName = $data['roomName'];
    $invitedBy = $data['invitedBy'];
    $invitedUser = $data['invitedUser'];
    $password = isset($data['password']) ? $data['password'] : null; // encrypted password for private rooms

    $stmt = $conn->prepare("INSERT INTO invitations (roomId, roomName, invitedBy, invitedUser, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $roomId, $roomName, $invitedBy, $invitedUser, $password);

    if ($stmt->execute()) {
        $response = ['status' => 'success'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to send invitation'];
    }

    $stmt->close();
}

echo json_encode($response);
$conn->close();
