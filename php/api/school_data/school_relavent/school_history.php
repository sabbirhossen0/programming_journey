<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db";  // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naming VARCHAR(255) NOT NULL,
    text TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // CREATE new entry
    $naming = $_POST['naming'] ?? '';
    $text = $_POST['text'] ?? '';

    if (!$naming || !$text) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    if (!isset($_FILES['image'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Image is required"]);
        exit;
    }

    $targetDir = "uploads/history/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO history (naming, text, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $naming, $text, $imageName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "History entry created"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // READ all entries
    $result = $conn->query("SELECT * FROM history ORDER BY created_at DESC");
    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    echo json_encode($entries);

} elseif ($method === 'PUT') {
    // UPDATE existing entry (id required)
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $naming = $putData['naming'] ?? null;
    $text = $putData['text'] ?? null;
    // Note: image update via PUT is tricky because $_FILES is not available here
    // We recommend using POST with a separate endpoint to update images or
    // handle image updates differently (e.g., base64 upload).

    if (!$id || !$naming || !$text) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, naming, and text are required for update"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE history SET naming = ?, text = ? WHERE id = ?");
    $stmt->bind_param("ssi", $naming, $text, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "History entry updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // DELETE by id
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    // Delete image file
    $res = $conn->query("SELECT image FROM history WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $imgPath = "uploads/history/" . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $stmt = $conn->prepare("DELETE FROM history WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "History entry deleted"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}

$conn->close();
?>
