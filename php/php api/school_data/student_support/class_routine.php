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
CREATE TABLE IF NOT EXISTS class_routine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class INT NOT NULL,
    date DATE NOT NULL,
    pdf VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

function uploadPDF($file, $oldFile = null) {
    $targetDir = "uploads/class_routine/";
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

if ($method === 'POST') {
    $title = $_POST['title'] ?? '';
    $class = $_POST['class'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!$title || !$class || !$date) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Title, class, and date are required"]);
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

    $stmt = $conn->prepare("INSERT INTO class_routine (title, class, date, pdf) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $title, $class, $date, $pdfName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Class routine created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    $result = $conn->query("SELECT * FROM class_routine ORDER BY class ASC");
    $routines = [];
    while ($row = $result->fetch_assoc()) {
        $routines[] = $row;
    }
    echo json_encode($routines);

} elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $title = $putData['title'] ?? null;
    $class = $putData['class'] ?? null;
    $date = $putData['date'] ?? null;

    if (!$id || !$title || !$class || !$date) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, title, class, and date are required"]);
        exit;
    }

    // PDF update via PUT not supported (due to no $_FILES support in PUT)

    $stmt = $conn->prepare("UPDATE class_routine SET title = ?, class = ?, date = ? WHERE id = ?");
    $stmt->bind_param("sisi", $title, $class, $date, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Class routine updated"]);
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

    // Delete associated PDF file if exists
    $res = $conn->query("SELECT pdf FROM class_routine WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['pdf']) {
            $pdfPath = "uploads/class_routine/" . $row['pdf'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM class_routine WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Class routine deleted"]);
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
