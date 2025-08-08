<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // DB username
$password = "";     // DB password
$dbname = "test_db"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// ✅ Create table automatically
$createTableSQL = "
CREATE TABLE IF NOT EXISTS staf (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    joining_date DATE NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Fetch all staff
    $result = $conn->query("SELECT * FROM staf ORDER BY joining_date DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

elseif ($method == 'POST') {
    // Insert new staff
    if (
        !empty($_POST['name']) && 
        !empty($_POST['role']) && 
        !empty($_POST['contact_number']) && 
        !empty($_POST['joining_date']) && 
        isset($_FILES['image'])
    ) {
        $name = $_POST['name'];
        $role = $_POST['role'];
        $contact_number = $_POST['contact_number'];
        $joining_date = $_POST['joining_date'];

        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO staf (name, image, role, contact_number, joining_date) 
                    VALUES ('$name', '$file_name', '$role', '$contact_number', '$joining_date')";
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success", "message" => "Staff added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "DB insert failed"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    }
}

elseif ($method == 'DELETE') {
    // Delete staff by ID
    parse_str(file_get_contents("php://input"), $deleteData);
    if (!empty($deleteData['id'])) {
        $id = intval($deleteData['id']);

        // Delete image from folder
        $res = $conn->query("SELECT image FROM staf WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $image_path = "uploads/" . $row['image'];
            if (file_exists($image_path)) unlink($image_path);
        }

        $conn->query("DELETE FROM staf WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Staff deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID missing"]);
    }
}

$conn->close();
?>
