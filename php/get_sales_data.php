<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "glh_db", 3306);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$sql = "SELECT category, SUM(amount) AS total 
        FROM sales 
        GROUP BY category";

$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "category" => $row["category"],
            "total" => (float)$row["total"]
        ];
    }
}

echo json_encode($data);
?>