<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "school_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]));
}

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    general_rules TEXT NOT NULL,
    behavior TEXT NOT NULL,
    absence TEXT NOT NULL,
    dresscode TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // CREATE new rule
    $general_rules = $_POST['general_rules'] ?? '';
    $behavior = $_POST['behavior'] ?? '';
    $absence = $_POST['absence'] ?? '';
    $dresscode = $_POST['dresscode'] ?? '';

    if (!$general_rules || !$behavior || !$absence || !$dresscode) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO rules (general_rules, behavior, absence, dresscode) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $general_rules, $behavior, $absence, $dresscode);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Rules created", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'GET') {
    // READ all rules
    $result = $conn->query("SELECT * FROM rules ORDER BY created_at DESC");
    $rulesArr = [];
    while ($row = $result->fetch_assoc()) {
        $rulesArr[] = $row;
    }
    echo json_encode($rulesArr);

} elseif ($method === 'PUT') {
    // UPDATE rule by id
    parse_str(file_get_contents("php://input"), $putData);

    $id = $putData['id'] ?? null;
    $general_rules = $putData['general_rules'] ?? null;
    $behavior = $putData['behavior'] ?? null;
    $absence = $putData['absence'] ?? null;
    $dresscode = $putData['dresscode'] ?? null;

    if (!$id || !$general_rules || !$behavior || !$absence || !$dresscode) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID and all fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE rules SET general_rules = ?, behavior = ?, absence = ?, dresscode = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $general_rules, $behavior, $absence, $dresscode, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Rules updated"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // DELETE rule by id
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM rules WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Rules deleted"]);
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
