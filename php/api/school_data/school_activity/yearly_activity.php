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
CREATE TABLE IF NOT EXISTS yearly_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    activity_date DATE NOT NULL,
    year VARCHAR(10) NOT NULL
)";
$conn->query($createTableSQL);

function uploadImage($file, $oldFile = null) {
    $targetDir = "uploads/yearly_activity/";
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
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $activity_date = $_POST['date'] ?? '';
    $year = $_POST['year'] ?? '';

    if (!$title || !$description || !$activity_date || !$year || !isset($_FILES['image'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $imageName = uploadImage($_FILES['image']);
    if ($imageName === false) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO yearly_activity (image, title, description, activity_date, year) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $imageName, $title, $description, $activity_date, $year);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Yearly activity added successfully", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    $result = $conn->query("SELECT * FROM yearly_activity ORDER BY activity_date DESC");
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    echo json_encode($activities);

} elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $title = $putData['title'] ?? null;
    $description = $putData['description'] ?? null;
    $activity_date = $putData['date'] ?? null;
    $year = $putData['year'] ?? null;

    if (!$id || !$title || !$description || !$activity_date || !$year) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID, title, description, date and year are required"]);
        exit;
    }

    // Get current image for this record
    $res = $conn->query("SELECT image FROM yearly_activity WHERE id=$id");
    if (!$res || $res->num_rows == 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }
    $row = $res->fetch_assoc();
    $currentImage = $row['image'];

    // Cannot upload image on PUT (files not handled) - to update image, use a separate POST endpoint or method
    // So here we just update text fields, image stays unchanged

    $stmt = $conn->prepare("UPDATE yearly_activity SET title = ?, description = ?, activity_date = ?, year = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $activity_date, $year, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Yearly activity updated"]);
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

    // Delete image file
    $res = $conn->query("SELECT image FROM yearly_activity WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $imgPath = "uploads/yearly_activity/" . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $stmt = $conn->prepare("DELETE FROM yearly_activity WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Yearly activity deleted successfully"]);
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
