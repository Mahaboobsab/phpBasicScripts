<?php
header('Content-Type: application/json');

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "eSociety");
if (!$conn) {
    echo json_encode(["status" => "failed", "message" => "Database connection error"]);
    exit;
}

// Fetch YOY spending data
$sql = "SELECT month, year_2024, year_2025 FROM YOYSpending ORDER BY FIELD(month, 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')";
$result = mysqli_query($conn, $sql);

$spendingData = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $spendingData[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $spendingData]);
} else {
    echo json_encode(["status" => "success", "data" => []]);
}

mysqli_close($conn);
?>
