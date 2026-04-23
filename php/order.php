<?php
$order_type = $_POST['order_type'];
$order_status = "Pending"; // default when order is created

$sql = "INSERT INTO orders (customer_id, order_type, order_status)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $customer_id, $order_type, $order_status);
$stmt->execute();

