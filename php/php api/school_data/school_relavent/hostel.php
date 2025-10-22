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
CREATE TABLE IF NOT EXISTS hostel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hostel_intro TEXT NOT NULL,
    benefit TEXT NOT NULL,
    rules TEXT NOT NULL,
    submit_process TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

function uploadImage($file, $oldImage = null) {
    $targetDir = "uploads/hostel/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($file['name']);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Delete old image if exists
        if ($oldImage) {
            $oldPath = $targetDir . $oldImage;
            if (file_exists($oldPath)) unlink($oldPath);
        }
        return $imageName;
    }
    return false;
}

if ($method === 'POST') {
    // CREATE new record
    $hostel_intro = $_POST['hostel_intro'] ?? '';
    $benefit = $_POST['benefit'] ?? '';
    $rules = $_POST['rules'] ?? '';
    $submit_process = $_POST['submit_process'] ?? '';

    if (!$hostel_intro || !$benefit || !$rules || !$submit_process) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All text fields are required"]);
        exit;
    }

    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadImage($_FILES['image']);
        if ($uploadResult === false) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Image upload failed"]);
            exit;
        }
        $imageName = $uploadResult;
    }

    $stmt = $conn->prepare("INSERT INTO hostel (hostel_intro, benefit, rules, submit_process, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $hostel_intro, $benefit, $rules, $submit_process, $imageName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Hostel info created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // READ all records
    $result = $conn->query("SELECT * FROM hostel ORDER BY created_at DESC");
    $hostels = [];
    while ($row = $result->fetch_assoc()) {
        $hostels[] = $row;
    }
    echo json_encode($hostels);

} elseif ($method === 'PUT') {
    // UPDATE record by id
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $hostel_intro = $putData['hostel_intro'] ?? null;
    $benefit = $putData['benefit'] ?? null;
    $rules = $putData['rules'] ?? null;
    $submit_process = $putData['submit_process'] ?? null;

    if (!$id || !$hostel_intro || !$benefit || !$rules || !$submit_process) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID and all text fields are required"]);
        exit;
    }

    // Get existing image to handle update if new image uploaded
    $res = $conn->query("SELECT image FROM hostel WHERE id = $id");
    $oldImage = null;
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $oldImage = $row['image'];
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        exit;
    }

    // Image upload during PUT is tricky because $_FILES is empty. 
    // One workaround is to check a separate POST request for image update or
    // use raw input for base64 image. 
    // For now, this code does NOT handle image update via PUT.

    $stmt = $conn->prepare("UPDATE hostel SET hostel_intro = ?, benefit = ?, rules = ?, submit_process = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $hostel_intro, $benefit, $rules, $submit_process, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Hostel info updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // DELETE record by id
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    // Delete image file
    $res = $conn->query("SELECT image FROM hostel WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $imgPath = "uploads/hostel/" . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $stmt = $conn->prepare("DELETE FROM hostel WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Hostel info deleted"]);
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
