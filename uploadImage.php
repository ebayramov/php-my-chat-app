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
    if (isset($_FILES['image']) && isset($_POST['roomId']) && isset($_POST['username'])) {
        $roomId = $_POST['roomId'];
        $username = $_POST['username'];
        $textMessage = $_POST['textMessage'];

        $target_dir = "uploads/";
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "http://localhost:8080/" . $target_file;

            $stmt = $conn->prepare("INSERT INTO messages (user, message, room_id, image_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $username, $textMessage, $roomId, $image_url);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'image_url' => $image_url]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save image URL in database.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
}

$conn->close();
