<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "testDatabase");
if (!$conn) {
    echo json_encode(["status" => "failed", "message" => "Database connection error"]);
    exit;
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Extract credentials
$emailStr = $data['email'] ?? null;
$passStr  = $data['password'] ?? null;

// Validate input
if (!$emailStr || !$passStr) {
    echo json_encode(["status" => "failed", "message" => "Missing email or password"]);
    exit;
}

// Prepare query
$sql = "SELECT * FROM Registration WHERE email = ? AND password = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(["status" => "failed", "message" => "Prepare failed: " . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($stmt, "ss", $emailStr, $passStr);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Respond with user data or error
if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(["status" => "success", "user" => $row]);
} else {
    echo json_encode(["status" => "failed", "message" => "Invalid credentials"]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
