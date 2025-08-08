<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $exam_name = $_POST['exam_name'] ?? '';
    $publication_date = $_POST['publication_date'] ?? '';
    $class = $_POST['class'] ?? '';
    $state = $_POST['state'] ?? '';

    if (!$exam_name || !$publication_date || !$class || !$state) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    // Handle optional PDF upload
    $pdfFileName = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/exam_results/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = basename($_FILES['pdf_file']['name']);
        $pdfFileName = time() . "_" . $originalName;
        $targetFile = $targetDir . $pdfFileName;

        $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($fileType !== "pdf") {
            echo json_encode(["status" => "error", "message" => "Only PDF files are allowed"]);
            exit;
        }

        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $targetFile)) {
            echo json_encode(["status" => "error", "message" => "PDF upload failed"]);
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO exam_results (exam_name, publication_date, class, state, pdf_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $exam_name, $publication_date, $class, $state, $pdfFileName);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Exam result added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    $result = $conn->query("SELECT * FROM exam_results ORDER BY publication_date DESC");
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode($results);

} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? 0;

    if ($id) {
        // Delete PDF file if exists
        $res = $conn->query("SELECT pdf_file FROM exam_results WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (!empty($row['pdf_file'])) {
                $pdfPath = "uploads/exam_results/" . $row['pdf_file'];
                if (file_exists($pdfPath)) unlink($pdfPath);
            }
        }

        $conn->query("DELETE FROM exam_results WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Exam result deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID is required"]);
    }
}

$conn->close();
?>
