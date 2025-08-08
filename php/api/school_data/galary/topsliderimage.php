<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // Your DB username
$password = "";     // Your DB password
$dbname = "test_db"; // Your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// ✅ Create table automatically
$createTableSQL = "
CREATE TABLE IF NOT EXISTS topslide_image (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    slide_date DATE NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Fetch all records
    $result = $conn->query("SELECT * FROM topslide_image ORDER BY slide_date DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

elseif ($method == 'POST') {
    // Insert new record
    if (!empty($_POST['title']) && !empty($_POST['slide_date']) && isset($_FILES['image'])) {
        $title = $_POST['title'];
        $slide_date = $_POST['slide_date'];

        $target_dir = "topslideupload/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO topslide_image (image, title, slide_date) 
                    VALUES ('$file_name', '$title', '$slide_date')";
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success", "message" => "Topslide image added"]);
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
    // Delete record by ID
    parse_str(file_get_contents("php://input"), $deleteData);
    if (!empty($deleteData['id'])) {
        $id = intval($deleteData['id']);

        // Delete image from uploads folder
        $res = $conn->query("SELECT image FROM topslide_image WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $image_path = "topslideupload/" . $row['image'];
            if (file_exists($image_path)) unlink($image_path);
        }

        $conn->query("DELETE FROM topslide_image WHERE id=$id");
        echo json_encode(["status" => "success", "message" => "Topslide image deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID missing"]);
    }
}

$conn->close();
?>
