<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: basket.php');
    exit;
}

$cart = $_SESSION['cart'];
$message = '';
$error = '';

function getSampleProduct($productId) {
    if ($productId === 1) {
        return [
            'id' => 1,
            'name' => 'Broccoli',
            'details' => 'Fresh, locally-sourced broccoli packed with nutrients and flavor.',
            'category' => 'Vegetables',
            'price' => 2.99,
            'discount' => 10,
            'image' => '../images/example-product.jpg'
        ];
    }
    return null;
}

function fetchProduct($conn, $productId) {
    $query = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    if (!$query) {
        return getSampleProduct($productId);
    }
    $query->bind_param("i", $productId);
    $query->execute();
    $result = $query->get_result();
    $product = $result->fetch_assoc();
    $query->close();

    return $product ?: getSampleProduct($productId);
}

function ensureOrdersTable($conn) {
    $createOrders = "
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_number VARCHAR(50) NOT NULL UNIQUE,
  status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  total DECIMAL(10,2) NOT NULL,
  points_earned INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($createOrders);
}

function ensureOrderItemsTable($conn) {
    $createOrderItems = "
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($createOrderItems);

    $checkColumn = $conn->query("SHOW COLUMNS FROM order_items LIKE 'product_name'");
    if ($checkColumn && $checkColumn->num_rows === 0) {
        $conn->query("ALTER TABLE order_items ADD COLUMN product_name VARCHAR(255) NOT NULL DEFAULT '' AFTER product_id");
    }
}

$items = [];
$subtotal = 0.0;

foreach ($cart as $productId => $quantity) {
    $product = fetchProduct($conn, (int)$productId);
    if (!$product) {
        continue;
    }
    $quantity = max(1, (int)$quantity);
    $unitPrice = floatval($product['price']);
    if (isset($product['discount']) && floatval($product['discount']) > 0) {
        $unitPrice *= (1 - floatval($product['discount']) / 100);
    }
    $itemTotal = $unitPrice * $quantity;
    $subtotal += $itemTotal;
    $items[] = [
        'id' => $productId,
        'name' => $product['name'],
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'subtotal' => $itemTotal,
    ];
}

$total = $subtotal;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $cardName = trim($_POST['card_name'] ?? '');
    $cardNumber = preg_replace('/\D+/', '', $_POST['card_number'] ?? '');
    $expiryMonth = trim($_POST['expiry_month'] ?? '');
    $expiryYear = trim($_POST['expiry_year'] ?? '');
    $cvc = trim($_POST['cvc'] ?? '');

    if (empty($cardName) || empty($cardNumber) || empty($expiryMonth) || empty($expiryYear) || empty($cvc)) {
        $error = 'Please complete all payment fields.';
    } elseif (!preg_match('/^\d{13,19}$/', $cardNumber)) {
        $error = 'Enter a valid card number (13-19 digits).';
    } elseif (!preg_match('/^[0-9]{2}$/', $expiryMonth) || (int)$expiryMonth < 1 || (int)$expiryMonth > 12) {
        $error = 'Enter a valid expiry month.';
    } elseif (!preg_match('/^[0-9]{2}$/', $expiryYear) || (int)$expiryYear < (int)date('y')) {
        $error = 'Enter a valid expiry year.';
    } elseif (!preg_match('/^[0-9]{3,4}$/', $cvc)) {
        $error = 'Enter a valid CVC code.';
    } else {
        ensureOrdersTable($conn);
        ensureOrderItemsTable($conn);

        $orderNumber = 'GLH' . time() . rand(100, 999);
        $insertOrder = $conn->prepare("INSERT INTO orders (user_id, order_number, status, total) VALUES (?, ?, 'Pending', ?)");
        $insertOrder->bind_param("isd", $_SESSION['user_id'], $orderNumber, $total);
        $insertOrder->execute();
        $orderId = $insertOrder->insert_id;
        $insertOrder->close();

        $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $insertItem->bind_param("iissdd", $orderId, $item['id'], $item['name'], $item['quantity'], $item['unit_price'], $item['subtotal']);
            $insertItem->execute();
        }
        $insertItem->close();

        $_SESSION['cart'] = [];
        header('Location: order_confirmation.php?order_id=' . $orderId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GLH</title>
    <link rel="stylesheet" href="../css/basket.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .checkout-form { max-width: 720px; margin: auto; text-align: left; }
        .checkout-form label { display:block; margin-bottom: 8px; font-weight: 600; }
        .checkout-form input, .checkout-form select { width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #ccc; }
        .checkout-form .payment-section, .checkout-form .summary-section { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 10px 24px rgba(0,0,0,.08); margin-bottom: 20px; }
        .checkout-form .summary-section table { width: 100%; border-collapse: collapse; }
        .checkout-form .summary-section th, .checkout-form .summary-section td { padding: 12px 8px; border-bottom: 1px solid #eee; }
        .checkout-form .summary-section th { text-align: left; }
        .checkout-form .error { background: #f8d7da; color: #721c24; padding: 14px; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 16px; }
        .checkout-form .note { font-size: 0.95rem; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="title">Secure Checkout</div>
        </header>

        <div class="checkout-form">
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="summary-section">
                <h2>Order Summary</h2>
                <table>
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>£<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>£<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3">Total</th><th>£<?php echo number_format($total, 2); ?></th></tr>
                    </tfoot>
                </table>
                <p class="note">This checkout page uses a sandbox payment gateway simulation. No real card will be charged.</p>
            </div>

            <div class="payment-section">
                <h2>Payment Details</h2>
                <form method="post">
                    <label for="card_name">Name on Card</label>
                    <input type="text" id="card_name" name="card_name" value="<?php echo htmlspecialchars($_POST['card_name'] ?? ''); ?>" required>

                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" value="<?php echo htmlspecialchars($_POST['card_number'] ?? ''); ?>" placeholder="1234 5678 9012 3456" required>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label for="expiry_month">Expiry Month (MM)</label>
                            <input type="text" id="expiry_month" name="expiry_month" value="<?php echo htmlspecialchars($_POST['expiry_month'] ?? ''); ?>" placeholder="MM" required>
                        </div>
                        <div>
                            <label for="expiry_year">Expiry Year (YY)</label>
                            <input type="text" id="expiry_year" name="expiry_year" value="<?php echo htmlspecialchars($_POST['expiry_year'] ?? ''); ?>" placeholder="YY" required>
                        </div>
                    </div>

                    <label for="cvc">CVC</label>
                    <input type="text" id="cvc" name="cvc" value="<?php echo htmlspecialchars($_POST['cvc'] ?? ''); ?>" placeholder="123" required>

                    <button type="submit" name="pay_now" style="background:#66bd3d;color:white;padding:14px 24px;border:none;border-radius:12px;cursor:pointer;">Pay £<?php echo number_format($total,2); ?></button>
                </form>
            </div>

            <a href="basket.php" style="display:inline-block; margin-top: 8px; color:#333; text-decoration:none;">← Back to basket</a>
        </div>
    </div>
</body>
</html>
