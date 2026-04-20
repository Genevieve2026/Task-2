<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];
$message = '';
$error = '';

define('SAMPLE_PRODUCT', json_encode([
    'id' => 1,
    'name' => 'Broccoli',
    'details' => 'Fresh, locally-sourced broccoli packed with nutrients and flavor.',
    'category' => 'Vegetables',
    'price' => 2.99,
    'discount' => 10,
    'image' => '../images/example-product.jpg'
]));

function getSampleProduct($productId) {
    $sample = json_decode(SAMPLE_PRODUCT, true);
    return $sample['id'] === $productId ? $sample : null;
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

    if ($product) {
        return $product;
    }

    return getSampleProduct($productId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        foreach ($_POST['quantity'] as $productId => $quantity) {
            $productId = (int)$productId;
            $quantity = max(0, (int)$quantity);
            if ($quantity === 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId] = $quantity;
            }
        }
        $message = 'Basket updated successfully.';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        unset($cart[$productId]);
        $message = 'Item removed from your basket.';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'clear') {
        $cart = [];
        $message = 'Your basket is now empty.';
    }
}

$items = [];
$subtotal = 0.0;

foreach ($cart as $productId => $quantity) {
    $product = fetchProduct($conn, $productId);
    if (!$product) {
        unset($cart[$productId]);
        continue;
    }

    $unitPrice = floatval($product['price']);
    if (isset($product['discount']) && floatval($product['discount']) > 0) {
        $unitPrice *= (1 - floatval($product['discount']) / 100);
    }

    $productTotal = $unitPrice * $quantity;
    $subtotal += $productTotal;

    $items[] = [
        'id' => $productId,
        'name' => $product['name'],
        'image' => $product['image'],
        'unit_price' => $unitPrice,
        'quantity' => $quantity,
        'total' => $productTotal,
    ];
}

$subtotal = round($subtotal, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLH Basket</title>
    <link rel="stylesheet" href="../css/basket.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="showCart">
    <div class="container">
        <header>
            <div class="title">Your Shopping Basket</div>
            <div class="cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo array_sum($cart); ?></span>
            </div>
        </header>

        <?php if (!empty($message)): ?>
            <div style="margin-bottom: 20px; padding: 12px; background: #e9f7ef; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <p>Your basket is empty. <a href="categories.php">Continue shopping</a>.</p>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <div class="listCart" style="display: grid; gap: 18px; text-align: left; margin-bottom: 24px;">
                    <?php foreach ($items as $item): ?>
                        <div class="item" style="grid-template-columns: 100px 1fr 100px 140px 120px;">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="name" style="text-align: left;">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            </div>
                            <div class="price">£<?php echo number_format($item['unit_price'], 2); ?></div>
                            <div class="quantity">
                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" style="width: 60px; padding: 6px; border-radius: 6px; border: 1px solid #ccc;">
                            </div>
                            <div class="totalPrice">£<?php echo number_format($item['total'], 2); ?></div>
                        </div>
                        <div style="display:flex; justify-content:flex-end; margin-bottom: 20px;">
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" style="background:#d44f4f;color:white;border:none;padding:8px 12px;border-radius:8px;cursor:pointer;">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" style="background:#66bd3d;color:white;padding:12px 20px;border:none;border-radius:10px;cursor:pointer;">Update basket</button>
            </form>

            <div style="margin-top: 24px;">
                <p><strong>Subtotal:</strong> £<?php echo number_format($subtotal, 2); ?></p>
                <form method="post" style="display:inline-block; margin-right: 12px;">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" style="background:#555;color:white;padding:12px 20px;border:none;border-radius:10px;cursor:pointer;">Clear basket</button>
                </form>
                <a href="checkout.php" style="display:inline-block; background:#66bd3d;color:white;padding:12px 20px;border-radius:10px;text-decoration:none;">Proceed to checkout</a>
            </div>
        <?php endif; ?>

        <br>
        <a href="categories.php" style="color:#333; text-decoration:none;">← Back to marketplace</a>
    </div>
</body>
</html>
