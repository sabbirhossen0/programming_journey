<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS notice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    pdf VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

function uploadPDF($file, $oldFile = null) {
    $targetDir = "uploads/notice/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($file['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Delete old file if exists
        if ($oldFile) {
            $oldPath = $targetDir . $oldFile;
            if (file_exists($oldPath)) unlink($oldPath);
        }
        return $fileName;
    }
    return false;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $date = $_POST['date'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? null;

    if (!$date || !$title) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Date and title are required"]);
        exit;
    }

    $pdfName = null;
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadPDF($_FILES['pdf']);
        if ($uploadResult === false) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "PDF upload failed"]);
            exit;
        }
        $pdfName = $uploadResult;
    }

    $stmt = $conn->prepare("INSERT INTO notice (date, title, description, pdf) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $date, $title, $description, $pdfName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Notice created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {

    // Get latest 5 notices: GET notice.php?limit=5

// Get all notices: GET notice.php


    $limit = isset($_GET['limit']) && intval($_GET['limit']) > 0 ? intval($_GET['limit']) : 0;

    if ($limit > 0) {
        $stmt = $conn->prepare("SELECT * FROM notice ORDER BY date DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query("SELECT * FROM notice ORDER BY date DESC");
    }

    $notices = [];
    while ($row = $result->fetch_assoc()) {
        $notices[] = $row;
    }
    echo json_encode($notices);

} elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $date = $putData['date'] ?? null;
    $title = $putData['title'] ?? null;
    $description = $putData['description'] ?? null;
    // PDF update via PUT not supported (use separate POST endpoint if needed)

    if (!$id || !$date || !$title) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, date and title are required"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE notice SET date = ?, title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssi", $date, $title, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Notice updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    // Delete pdf file if exists
    $res = $conn->query("SELECT pdf FROM notice WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['pdf']) {
            $pdfPath = "uploads/notice/" . $row['pdf'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM notice WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Notice deleted"]);
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
