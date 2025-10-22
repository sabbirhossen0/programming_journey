<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db"; // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Automatically create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS assistant_head_teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    speech TEXT,
    designation VARCHAR(100),
    joining_date DATE NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $name        = $_POST['name'] ?? '';
    $email       = $_POST['email'] ?? '';
    $contact     = $_POST['contact'] ?? '';
    $speech      = $_POST['speech'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $joining_date = $_POST['joining_date'] ?? '';

    // Image upload
    $imagePath = '';
    if (isset($_FILES['image'])) {
        $targetDir  = "assheadteacher/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageName  = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO assistant_head_teacher (name, email, contact, image, speech, designation, joining_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $contact, $imagePath, $speech, $designation, $joining_date);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Assistant Head Teacher added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
    $stmt->close();
} elseif ($method === 'GET') {
    $result = $conn->query("SELECT * FROM assistant_head_teacher ORDER BY joining_date DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'] ?? '';
    if ($id) {
        // Delete image file if exists
        $res = $conn->query("SELECT image FROM assistant_head_teacher WHERE id = $id");
       if ($res->num_rows > 0) {
            $imgRow = $res->fetch_assoc();
            $imgPath = "assheadteacher/" . $imgRow['image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }


        $stmt = $conn->prepare("DELETE FROM assistant_head_teacher WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Assistant Head Teacher deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "ID is required"]);
    }
}

$conn->close();
?>
