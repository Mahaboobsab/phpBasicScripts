<?php
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "eSociety");
if (!$conn) {
    echo json_encode(["status" => "failed", "message" => "Database connection error"]);
    exit;
}

// Fetch all activities
$sql = "SELECT activity_id, title, activity_date, location, is_completed FROM Activities ORDER BY activity_date ASC";
$result = mysqli_query($conn, $sql);

$activities = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = $row;
    }
    echo json_encode(["status" => "success", "activities" => $activities]);
} else {
    echo json_encode(["status" => "success", "activities" => []]);
}

mysqli_close($conn);
?>
