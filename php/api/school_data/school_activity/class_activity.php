<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
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

    // Handle image upload
    $imagePath = '';
    $targetDir = "uploads/class_activity/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $imageName;  // store just filename, folder known
    } else {
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }

    // Insert record
    $stmt = $conn->prepare("INSERT INTO class_activity (image, title, description, activity_date, dept) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $imagePath, $title, $description, $activity_date, $dept);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Class activity added successfully"]);
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
}

$conn->close();
?>
