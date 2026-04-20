<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    header('Location: users.php');
    exit;
}

$query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$query->bind_param("ii", $orderId, $_SESSION['user_id']);
$query->execute();
$orderResult = $query->get_result();
$order = $orderResult->fetch_assoc();
$query->close();

if (!$order) {
    header('Location: users.php');
    exit;
}

$items = [];
$itemQuery = $conn->prepare("SELECT quantity, unit_price, subtotal, product_name FROM order_items WHERE order_id = ?");
$itemQuery->bind_param("i", $orderId);
$itemQuery->execute();
$itemResult = $itemQuery->get_result();
while ($row = $itemResult->fetch_assoc()) {
    $items[] = $row;
}
$itemQuery->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/basket.css">
</head>
<body class="showCart">
    <div class="container">
        <header>
            <div class="title">Order Confirmed</div>
        </header>

        <div style="margin-top: 24px; text-align: left; background: #fff; padding: 24px; border-radius: 18px; box-shadow: 0 14px 34px rgba(0,0,0,.08);">
            <h2>Thank you, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
            <p>Your order has been placed successfully.</p>
            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            <p><strong>Total Paid:</strong> £<?php echo number_format($order['total'], 2); ?></p>

            <h3>Order Items</h3>
            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name'] ?: 'Unknown product'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>£<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td>£<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top: 18px;">You will earn loyalty points once this order is marked Delivered.</p>
            <a href="users.php" style="display:inline-block; margin-top: 16px; padding: 12px 20px; background:#66bd3d; color:white; border-radius:10px; text-decoration:none;">Go to dashboard</a>
        </div>
    </div>
</body>
</html>
