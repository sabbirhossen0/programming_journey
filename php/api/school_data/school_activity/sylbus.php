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
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS syllabus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publication_date DATE NOT NULL,
    class INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    pdf_file VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

function uploadPDF($file, $oldFile = null) {
    $targetDir = "uploads/syllabus/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($file['name']);
    $targetFile = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileType !== "pdf") {
        return false;
    }

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
    $publication_date = $_POST['publication_date'] ?? '';
    $class = $_POST['class'] ?? '';
    $title = $_POST['title'] ?? '';

    if (!$publication_date || !$class || !$title) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    if (!filter_var($class, FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Class must be an integer"]);
        exit;
    }

    $pdfFileName = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadPDF($_FILES['pdf_file']);
        if ($uploadResult === false) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Only PDF files are allowed or upload failed"]);
            exit;
        }
        $pdfFileName = $uploadResult;
    }

    $stmt = $conn->prepare("INSERT INTO syllabus (publication_date, class, title, pdf_file) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $publication_date, $class, $title, $pdfFileName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Syllabus added successfully", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    $result = $conn->query("SELECT * FROM syllabus ORDER BY class ASC");
    $syllabi = [];
    while ($row = $result->fetch_assoc()) {
        $syllabi[] = $row;
    }
    echo json_encode($syllabi);

} elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $publication_date = $putData['publication_date'] ?? null;
    $class = $putData['class'] ?? null;
    $title = $putData['title'] ?? null;

    if (!$id || !$publication_date || !$class || !$title) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, publication_date, class and title are required"]);
        exit;
    }

    if (!filter_var($class, FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Class must be an integer"]);
        exit;
    }

    // Get existing pdf filename
    $res = $conn->query("SELECT pdf_file FROM syllabus WHERE id = $id");
    if (!$res || $res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }
    $row = $res->fetch_assoc();
    $oldPdf = $row['pdf_file'];

    // Cannot handle file uploads in PUT directly, so update pdf separately if needed
    $stmt = $conn->prepare("UPDATE syllabus SET publication_date = ?, class = ?, title = ? WHERE id = ?");
    $stmt->bind_param("sisi", $publication_date, $class, $title, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Syllabus updated"]);
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

    // Delete PDF file if exists
    $res = $conn->query("SELECT pdf_file FROM syllabus WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['pdf_file'])) {
            $pdfPath = "uploads/syllabus/" . $row['pdf_file'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM syllabus WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Syllabus deleted successfully"]);
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
