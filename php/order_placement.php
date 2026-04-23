  <?php
require 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $order_type = $_POST['order_type'];
    $order_status = "Pending";

    $sql = "INSERT INTO orders (customer_name, order_type, order_status)
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $customer_name, $order_type, $order_status);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        $message = "Order placed! Your Order ID is: " . $order_id;
    } else {
        $message = "Error placing order.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Place your Order!</title>
    <link rel="stylesheet" href="../css/order_placement.css">
</head>
<body>
<h2>Delivery and Collection</h2>

<?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Customer Name:</label>
    <input type="text" name="customer_name" required>

    <h3>Choose how you want to receive your order</h3>
    <label>
        <input type="radio" name="order_type" value="delivery" required>
        Delivery
    </label>
    <label>
        <input type="radio" name="order_type" value="collection" required>
        Collection
    </label>

    <button type="submit">Place Order</button>
</form>

<p><a href="track.php">Track an order</a></p>

</body>
</html>

