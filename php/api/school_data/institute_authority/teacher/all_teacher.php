<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // DB username
$password = "";     // DB password
$dbname = "test_db"; // DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// ✅ Create table automatically if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    image VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    role VARCHAR(100) NOT NULL,
    joining_date DATE NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Fetch all teachers
    $result = $conn->query("SELECT * FROM teacher ORDER BY joining_date DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

elseif ($method == 'POST') {
    // Insert new teacher
    if (
        !empty($_POST['name']) &&
        !empty($_POST['email']) &&
        !empty($_POST['contact_number']) &&
        !empty($_POST['role']) &&
        !empty($_POST['joining_date']) &&
        isset($_FILES['image'])
    ) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $role = $_POST['role'];
        $joining_date = $_POST['joining_date'];

        $target_dir = "allteacher/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO teacher (name, email, image, contact_number, role, joining_date) 
                    VALUES ('$name', '$email', '$file_name', '$contact_number', '$role', '$joining_date')";
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success", "message" => "Teacher added successfully"]);
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
    // Delete teacher by ID
    parse_str(file_get_contents("php://input"), $deleteData);
    if (!empty($deleteData['id'])) {
        $id = intval($deleteData['id']);

        // Delete image file
        $res = $conn->query("SELECT image FROM teacher WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $image_path = "allteacher/" . $row['image'];
            if (file_exists($image_path)) unlink($image_path);
        }

        $conn->query("DELETE FROM teacher WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Teacher deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID missing"]);
    }
}

$conn->close();
?>
