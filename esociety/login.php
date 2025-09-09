<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "eSociety");
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
$sql = "SELECT admin_id, name, email, role, password FROM Admin WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(["status" => "failed", "message" => "Prepare failed: " . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($stmt, "s", $emailStr);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verify credentials
if ($row = mysqli_fetch_assoc($result)) {
    if ($row['password'] === $passStr) {
        unset($row['password']); // Hide password in response
        echo json_encode(["status" => "success", "admin" => $row]);
    } else {
        echo json_encode(["status" => "failed", "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["status" => "failed", "message" => "Admin not found"]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
