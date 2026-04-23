<?php
session_start();
include 'config.php';

// initialise cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($qty < 1) $qty = 1;

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }

    // redirect to avoid resubmitting form
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// REMOVE ITEM
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: basket.php");
    exit();
}

// UPDATE QUANTITY
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }

    header("Location: basket.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/basket.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Your Basket</title>
</head>
<body>

<div class="basket-container">
    <h1>Your Basket</h1>
    <span class="back-link"><a href="../php/categories.php"><i class="fas fa-arrow-left"></i> Keep Shopping!</a></span><br>
    <span class="back-link"><a href="../php/users.php"> Go to User Dashboard</a></span>

    <?php if (!empty($_SESSION['cart'])): ?>

    <form method="POST">

        <div class="basket-items">

        <?php
        $total = 0;

        foreach ($_SESSION['cart'] as $id => $qty):
            $id = (int)$id;

            $result = $conn->query("SELECT * FROM products WHERE id = $id");
            $product = $result->fetch_assoc();

            if (!$product) continue;

            $price = (float)$product['price'];
            $discount = (float)$product['discount'];
            $finalPrice = $discount > 0 ? $price * (1 - $discount / 100) : $price;

            $subtotal = $finalPrice * $qty;
            $total += $subtotal;
        ?>

            <div class="basket-item">

                <div class="item-left">
                    <div class="item-name">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </div>

                    <div class="item-price">
                        <?php if ($discount > 0): ?>
                            <span class="price-old">£<?php echo number_format($price, 2); ?></span>
                            <span class="price-new">£<?php echo number_format($finalPrice, 2); ?></span>
                        <?php else: ?>
                            <span class="price">£<?php echo number_format($price, 2); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="item-right">

                    <input type="number"
                        name="quantities[<?php echo $id; ?>]"
                        value="<?php echo $qty; ?>"
                        min="0"
                        class="qty-input">

                    <div class="subtotal">
                        £<?php echo number_format($subtotal, 2); ?>
                    </div>

                    <a href="basket.php?remove=<?php echo $id; ?>" class="remove-btn">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

        </div>

        <div class="basket-summary">
            <h2>Total: £<?php echo number_format($total, 2); ?></h2>
        </div>

    </form>

    <form method="POST" action="checkout.php">
        <button type="submit" class="btn primary">
            Proceed to Checkout
        </button>
    </form>

    <?php else: ?>
        <p class="empty">Your basket is empty</p>
    <?php endif; ?>

</div>

</body>
</html>