<?php
require 'config.php';

$order = null;
$error = "";
$progress_percent = 0;

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        $status = $order['order_status'];
        $progress_map = [
            "Pending" => 10,
            "Preparing" => 40,
            "Ready for Collection" => 70,
            "Out for Delivery" => 70,
            "Completed" => 100
        ];
        $progress_percent = isset($progress_map[$status]) ? $progress_map[$status] : 0;
    } else {
        $error = "Order not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Track Order</title>
    <link rel="stylesheet" href="../css/order_placement.css">
</head>
<body>
<h2>Track Your Order</h2>

<form method="GET">
    <label>Enter your Order ID:</label>
    <input type="number" name="order_id" required>
    <button type="submit">Track</button>
</form>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($order): ?>
    <h3>Order ID: <?php echo $order['id']; ?></h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Type:</strong> <?php echo $order['order_type']; ?></p>
    <p><strong>Status:</strong> <?php echo $order['order_status']; ?></p>

    <div class="progress">
        <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%;"></div>
    </div>
<?php endif; ?>

<p><a href="checkout.php">Back to checkout</a></p>
</body>
</html>