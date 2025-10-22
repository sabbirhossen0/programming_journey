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
CREATE TABLE IF NOT EXISTS library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    library_intro TEXT NOT NULL,
    book_collection TEXT NOT NULL,
    rules TEXT NOT NULL,
    time VARCHAR(100) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

function uploadImage($file, $oldImage = null) {
    $targetDir = "uploads/library/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($file['name']);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
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
    $library_intro = $_POST['library_intro'] ?? '';
    $book_collection = $_POST['book_collection'] ?? '';
    $rules = $_POST['rules'] ?? '';
    $time = $_POST['time'] ?? '';

    if (!$library_intro || !$book_collection || !$rules || !$time) {
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

    $stmt = $conn->prepare("INSERT INTO library (library_intro, book_collection, rules, time, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $library_intro, $book_collection, $rules, $time, $imageName);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Library info created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // READ all records
    $result = $conn->query("SELECT * FROM library ORDER BY created_at DESC");
    $libraries = [];
    while ($row = $result->fetch_assoc()) {
        $libraries[] = $row;
    }
    echo json_encode($libraries);

} elseif ($method === 'PUT') {
    // UPDATE record by id
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $library_intro = $putData['library_intro'] ?? null;
    $book_collection = $putData['book_collection'] ?? null;
    $rules = $putData['rules'] ?? null;
    $time = $putData['time'] ?? null;

    if (!$id || !$library_intro || !$book_collection || !$rules || !$time) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID and all text fields are required"]);
        exit;
    }

    // Image update is NOT supported here because PHP doesn't support $_FILES on PUT.
    // You can create a separate image update endpoint if needed.

    $stmt = $conn->prepare("UPDATE library SET library_intro = ?, book_collection = ?, rules = ?, time = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $library_intro, $book_collection, $rules, $time, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Library info updated"]);
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
    $res = $conn->query("SELECT image FROM library WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $imgPath = "uploads/library/" . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $stmt = $conn->prepare("DELETE FROM library WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Library info deleted"]);
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
