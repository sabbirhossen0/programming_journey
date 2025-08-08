<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db"; // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS school_introduction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mission TEXT NOT NULL,
    vision TEXT NOT NULL,
    objective TEXT NOT NULL,
    benefit TEXT NOT NULL,
    specialty TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // CREATE new intro
    $mission = $_POST['mission'] ?? '';
    $vision = $_POST['vision'] ?? '';
    $objective = $_POST['objective'] ?? '';
    $benefit = $_POST['benefit'] ?? '';
    $specialty = $_POST['specialty'] ?? '';

    if (!$mission || !$vision || !$objective || !$benefit || !$specialty) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO school_introduction (mission, vision, objective, benefit, specialty) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $mission, $vision, $objective, $benefit, $specialty);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Introduction created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // READ all intros
    $result = $conn->query("SELECT * FROM school_introduction ORDER BY created_at DESC");
    $intros = [];
    while ($row = $result->fetch_assoc()) {
        $intros[] = $row;
    }
    echo json_encode($intros);

} elseif ($method === 'PUT') {
    // UPDATE intro by id
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $mission = $putData['mission'] ?? null;
    $vision = $putData['vision'] ?? null;
    $objective = $putData['objective'] ?? null;
    $benefit = $putData['benefit'] ?? null;
    $specialty = $putData['specialty'] ?? null;

    if (!$id || !$mission || !$vision || !$objective || !$benefit || !$specialty) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields and ID are required"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE school_introduction SET mission=?, vision=?, objective=?, benefit=?, specialty=? WHERE id=?");
    $stmt->bind_param("sssssi", $mission, $vision, $objective, $benefit, $specialty, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Introduction updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // DELETE intro by id
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM school_introduction WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Introduction deleted"]);
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
