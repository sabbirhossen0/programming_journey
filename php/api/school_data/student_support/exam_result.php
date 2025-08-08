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
CREATE TABLE IF NOT EXISTS exam_result (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(255) NOT NULL,
    class INT NOT NULL,
    state VARCHAR(100) NOT NULL,
    date_of_publish DATE NOT NULL,
    pdf VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

function uploadPDF($file, $oldFile = null) {
    $targetDir = "uploads/exam_result/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($file['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        if ($oldFile) {
            $oldPath = $targetDir . $oldFile;
            if (file_exists($oldPath)) unlink($oldPath);
        }
        return $fileName;
    }
    return false;
}

if ($method === 'POST') {
    // Create new exam result
    $exam_name = $_POST['exam_name'] ?? '';
    $class = $_POST['class'] ?? '';
    $state = $_POST['state'] ?? '';
    $date_of_publish = $_POST['date_of_publish'] ?? '';

    if (!$exam_name || !$class || !$state || !$date_of_publish) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "exam_name, class, state, and date_of_publish are required"]);
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

    $stmt = $conn->prepare("INSERT INTO exam_result (exam_name, class, state, date_of_publish, pdf) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $exam_name, $class, $state, $date_of_publish, $pdfName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Exam result created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // Get all exam results ordered by date_of_publish DESC
    $result = $conn->query("SELECT * FROM exam_result ORDER BY date_of_publish DESC");
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode($results);

} elseif ($method === 'PUT') {
    // Update only state, date_of_publish, pdf by id
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $state = $putData['state'] ?? null;
    $date_of_publish = $putData['date_of_publish'] ?? null;

    if (!$id || !$state || !$date_of_publish) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, state, and date_of_publish are required"]);
        exit;
    }

    // Get old pdf filename for deletion if updated
    $res = $conn->query("SELECT pdf FROM exam_result WHERE id = $id");
    if (!$res || $res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }
    $row = $res->fetch_assoc();
    $oldPdf = $row['pdf'];

    // Check if a new PDF is sent via a separate POST file upload? 
    // PHP does not support $_FILES with PUT method.
    // So to update PDF, you'll likely need a separate endpoint or do via POST.

    // For this example, only update state and date_of_publish here
    $stmt = $conn->prepare("UPDATE exam_result SET state = ?, date_of_publish = ? WHERE id = ?");
    $stmt->bind_param("ssi", $state, $date_of_publish, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Exam result updated (state and date_of_publish)"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // Delete exam result by id, including PDF file if exists
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    $res = $conn->query("SELECT pdf FROM exam_result WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['pdf']) {
            $pdfPath = "uploads/exam_result/" . $row['pdf'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM exam_result WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Exam result deleted"]);
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
