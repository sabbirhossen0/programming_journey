<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "school_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed."]));
}

header("Content-Type: application/json");

// ✅ Create table automatically if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS head_teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    contact_number VARCHAR(50) NOT NULL,
    role VARCHAR(100) NOT NULL,
    joining_date DATE NOT NULL,
    speech_date DATE NOT NULL,
    speech TEXT NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "POST") {
    // Add new head teacher
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    $role = $_POST['role'] ?? '';
    $joining_date = $_POST['joining_date'] ?? '';
    $speech_date = $_POST['speech_date'] ?? '';
    $speech = $_POST['speech'] ?? '';

    // Handle image upload
    $imageName = "";
    if (isset($_FILES['image'])) {
        $targetDir = "headteacher/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $imageName);
    }

    $sql = "INSERT INTO head_teacher 
        (name, email, image, contact_number, role, joining_date, speech_date, speech) 
        VALUES ('$name', '$email', '$imageName', '$contact', '$role', '$joining_date', '$speech_date', '$speech')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Head Teacher added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

} elseif ($method === "GET") {
    // Fetch all head teachers
    $result = $conn->query("SELECT * FROM head_teacher ORDER BY id DESC");
    $teachers = [];
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
    echo json_encode($teachers);

} elseif ($method === "DELETE") {
    // Delete head teacher by ID
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? 0;

    if ($id) {
        // Delete image file from server
        $res = $conn->query("SELECT image FROM head_teacher WHERE id=$id");
        if ($res->num_rows > 0) {
            $imgRow = $res->fetch_assoc();
            $imgPath = "headteacher/" . $imgRow['image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }

        $conn->query("DELETE FROM head_teacher WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID is required"]);
    }
}

$conn->close();
?>
