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

// Required fields
$nameStr     = $data['name']      ?? null;
$clgStr      = $data['college']   ?? '';
$branchStr   = $data['branch']    ?? '';
$genderStr   = $data['gender']    ?? '';
$dateStr     = $data['date']      ?? '';
$teleStr     = $data['telephone'] ?? '';
$eventStr    = $data['event']     ?? '';
$emailStr    = $data['email']     ?? null;
$passStr     = $data['password']  ?? null;

// Validate required fields
if (!$nameStr || !$emailStr || !$passStr) {
    echo json_encode(["status" => "failed", "message" => "Missing required fields"]);
    exit;
}

// Check for duplicate email
$checkQuery = "SELECT id FROM Registration WHERE email = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
if (!$checkStmt) {
    echo json_encode(["status" => "failed", "message" => "Prepare failed: " . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($checkStmt, "s", $emailStr);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    echo json_encode(["status" => "failed", "message" => "Email already registered"]);
    mysqli_stmt_close($checkStmt);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_close($checkStmt);

// Insert query
$insertQuery = "INSERT INTO Registration (name, college, branch, gender, date, telephone, event, email, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$insertStmt = mysqli_prepare($conn, $insertQuery);
if (!$insertStmt) {
    echo json_encode(["status" => "failed", "message" => "Prepare failed: " . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($insertStmt, "sssssssss", $nameStr, $clgStr, $branchStr, $genderStr, $dateStr, $teleStr, $eventStr, $emailStr, $passStr);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode(["status" => "success", "message" => "Record inserted"]);
} else {
    echo json_encode(["status" => "failed", "message" => "Insert failed: " . mysqli_error($conn)]);
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);
?>
