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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) || isset($_POST['message'])) {
        $roomId = $_POST['roomId'];
        $username = $_POST['username'];
        $textMessage = isset($_POST['message']) ? $_POST['message'] : '';

        $image_url = null;

        if (isset($_FILES['image'])) {
            $target_dir = "uploads/";
            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "http://localhost:8080/" . $target_file;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO messages (user, message, room_id, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $username, $textMessage, $roomId, $image_url);

        if ($stmt->execute()) {
            $messageId = $stmt->insert_id;
            echo json_encode(['status' => 'success', 'image_url' => $image_url, 'id' => $messageId ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save message in database.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} 




else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['room_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'room_id is required']);
        exit;
    }
    $room_id = $_GET['room_id'];
    $messagesResult = $conn->query("SELECT * FROM messages WHERE room_id = $room_id ORDER BY created_at ASC");
    $roomsResult = $conn->query("SELECT * FROM rooms");

    $messages = [];
    while ($row = $messagesResult->fetch_assoc()) {
        $messages[] = $row;
    }

    $rooms = [];
    while ($row = $roomsResult->fetch_assoc()) {
        $rooms[] = $row;
    }
    echo json_encode(['messages' => $messages, 'rooms' => $rooms]);
}

$conn->close();
