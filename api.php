<?php
// Set headers to allow CORS and JSON format
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");

// Database configuration
$host = "localhost";
$db_name = "info_delivery_system";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle GET request: fetch all records
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM information ORDER BY id DESC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
        exit;
    }

    // Handle POST request: insert new record
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['title']) || !isset($data['content'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing title or content"]);
            exit;
        }

        $title = trim($data['title']);
        $content = trim($data['content']);

        $stmt = $pdo->prepare("INSERT INTO information (title, content) VALUES (:title, :content)");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Information saved successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to save information"]);
        }
        exit;
    }

    // If method is not GET or POST
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
