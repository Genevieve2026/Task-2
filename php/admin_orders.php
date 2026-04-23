<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <link rel="stylesheet" href="../css/order_placement.css">
</head>
<body>
<h2>Admin – Manage Orders</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Type</th>
        <th>Status</th>
        <th>Update</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['customer_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['order_type'] ?? ''); ?></td>
            <?php $status = $row['order_status'] ?? ''; ?>
            <td>
                <form method="POST" class="inline-form">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <select name="order_status">
                        <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Preparing" <?= $status === 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                        <option value="Ready for Collection" <?= $status === 'Ready for Collection' ? 'selected' : '' ?>>Ready for Collection</option>
                        <option value="Out for Delivery" <?= $status === 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                        <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>

                    </select>
                    <button type="submit">Save</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<p><a href="checkout.php">Back to checkout</a></p>
</body>
</html>