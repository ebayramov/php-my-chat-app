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

    if (isset($data['id'])) {
        $messageId = $data['id'];

        // First, retrieve the image URL for the message (if any)
        $sql = "SELECT image_url FROM messages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $stmt->bind_result($imageUrl);
        $stmt->fetch();
        $stmt->close();

        // Check if there is an image associated with this message
        if ($imageUrl) {
            // Extract the file path from the URL (assuming the uploads folder is in the root directory)
            $imagePath = str_replace("http://localhost:8080/", "", $imageUrl);

            // Check if the file exists, then delete it
            if (file_exists($imagePath)) {
                if (!unlink($imagePath)) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete image from server.']);
                    exit;
                }
            }
        }

        // Now, delete the message from the database
        $sql = "DELETE FROM messages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $messageId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete message.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
