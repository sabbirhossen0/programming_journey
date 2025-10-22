<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db"; // Change this to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(255) NOT NULL,
    publication_date DATE NOT NULL,
    class VARCHAR(50) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pdf_file VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

function uploadPDF($file, $oldFile = null) {
    $targetDir = "uploads/exam_results/";
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
    // Create
    $exam_name = $_POST['exam_name'] ?? '';
    $publication_date = $_POST['publication_date'] ?? '';
    $class = $_POST['class'] ?? '';
    $state = $_POST['state'] ?? '';

    if (!$exam_name || !$publication_date || !$class || !$state) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
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

    $stmt = $conn->prepare("INSERT INTO exam_results (exam_name, publication_date, class, state, pdf_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $exam_name, $publication_date, $class, $state, $pdfFileName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Exam result added successfully", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // Read all
    $result = $conn->query("SELECT * FROM exam_results ORDER BY publication_date DESC");
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode($results);

} elseif ($method === 'PUT') {
    // Update
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $exam_name = $putData['exam_name'] ?? null;
    $publication_date = $putData['publication_date'] ?? null;
    $class = $putData['class'] ?? null;
    $state = $putData['state'] ?? null;

    if (!$id || !$exam_name || !$publication_date || !$class || !$state) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, exam_name, publication_date, class and state are required"]);
        exit;
    }

    // Get existing pdf filename for possible deletion later
    $res = $conn->query("SELECT pdf_file FROM exam_results WHERE id = $id");
    if (!$res || $res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }
    $row = $res->fetch_assoc();
    $oldPdf = $row['pdf_file'];

    // We cannot receive files via PUT directly, so PDF update requires a separate POST request or workaround.
    // For now, update other fields only
    $stmt = $conn->prepare("UPDATE exam_results SET exam_name = ?, publication_date = ?, class = ?, state = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $exam_name, $publication_date, $class, $state, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Exam result updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // Delete
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    // Delete PDF file if exists
    $res = $conn->query("SELECT pdf_file FROM exam_results WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['pdf_file'])) {
            $pdfPath = "uploads/exam_results/" . $row['pdf_file'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM exam_results WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Exam result deleted successfully"]);
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
