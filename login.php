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

$response = ['status' => 'error', 'message' => 'Invalid action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action'])) {
    $username = $data['username'];
    $password = $data['password'];
    $action = $data['action'];

    if ($action === 'create') {
        // Create profile
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'username' => $username];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to create profile'];
        }
        $stmt->close();
    } elseif ($action === 'login') {
        // Login
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $response = ['status' => 'success', 'username' => $username];
        } else {
            $response = ['status' => 'error', 'message' => 'Invalid username or password'];
        }
        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
