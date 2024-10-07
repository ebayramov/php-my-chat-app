<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    $username = $_GET['username'];

    $stmt = $conn->prepare("SELECT * FROM invitations WHERE invitedUser = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $invitations = [];
    while ($row = $result->fetch_assoc()) {
        $invitations[] = $row;
    }

    $response = ['status' => 'success', 'invitations' => $invitations];

    $stmt->close();
}

echo json_encode($response);
$conn->close();
