<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // your DB username
$password = ""; // your DB password
$dbname = "school_db"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// ✅ Create table automatically if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS committee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // FETCH all records
    $result = $conn->query("SELECT * FROM committee");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

elseif ($method == 'POST') {
    // INSERT new record
    if (!empty($_POST['name']) && !empty($_POST['role']) && isset($_FILES['image'])) {
        $name = $_POST['name'];
        $role = $_POST['role'];

        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO committee (name, image, role) VALUES ('$name', '$file_name', '$role')";
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success", "message" => "Member added successfully"]);
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
    // DELETE record by ID
    parse_str(file_get_contents("php://input"), $deleteData);
    if (!empty($deleteData['id'])) {
        $id = intval($deleteData['id']);

        // Get image path to delete from folder
        $res = $conn->query("SELECT image FROM committee WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $image_path = "uploads/" . $row['image'];
            if (file_exists($image_path)) unlink($image_path);
        }

        $conn->query("DELETE FROM committee WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Member deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID missing"]);
    }
}

$conn->close();
?>
