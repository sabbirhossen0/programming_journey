<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db";  // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table automatically if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS class_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    activity_date DATE NOT NULL,
    dept VARCHAR(100) NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

function uploadImage($file, $oldFile = null) {
    $targetDir = "uploads/class_activity/";
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
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $activity_date = $_POST['date'] ?? '';
    $dept        = $_POST['dept'] ?? '';

    // Validate required fields
    if (!$title || !$description || !$activity_date || !$dept || !isset($_FILES['image'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $imageName = uploadImage($_FILES['image']);
    if (!$imageName) {
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO class_activity (image, title, description, activity_date, dept) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $imageName, $title, $description, $activity_date, $dept);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Class activity added successfully", "id" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // Fetch all class activities
    $result = $conn->query("SELECT * FROM class_activity ORDER BY activity_date DESC");
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    echo json_encode($activities);

} elseif ($method === 'PUT') {
    // PUT data is not in $_POST or $_FILES, parse input and handle image upload separately
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $title = $putData['title'] ?? null;
    $description = $putData['description'] ?? null;
    $activity_date = $putData['date'] ?? null;
    $dept = $putData['dept'] ?? null;

    if (!$id || !$title || !$description || !$activity_date || !$dept) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, title, description, date, and dept are required"]);
        exit;
    }

    // Get existing image filename
    $res = $conn->query("SELECT image FROM class_activity WHERE id = $id");
    if (!$res || $res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }
    $row = $res->fetch_assoc();
    $oldImage = $row['image'];

    // PHP doesn't support file uploads with PUT, so to update image you must use POST + _method=PUT or separate endpoint
    // For now, we update only text fields and keep old image
    $stmt = $conn->prepare("UPDATE class_activity SET title = ?, description = ?, activity_date = ?, dept = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $activity_date, $dept, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Class activity updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? 0;

    if ($id) {
        // Delete image file
        $res = $conn->query("SELECT image FROM class_activity WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $imgPath = "uploads/class_activity/" . $row['image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }

        $conn->query("DELETE FROM class_activity WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Class activity deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID is required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}

$conn->close();
