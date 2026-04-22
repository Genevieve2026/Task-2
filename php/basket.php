<?php
session_start();
include 'config.php';

// Initialise basket if not exists
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// Add item to basket
if (isset($_POST['add_to_basket'])) {
    $product_id = intval($_POST['product_id']);

    // If already in basket → increase quantity
    if (isset($_SESSION['basket'][$product_id])) {
        $_SESSION['basket'][$product_id]++;
    } else {
        $_SESSION['basket'][$product_id] = 1;
    }
}

// Fetch product details for items in basket
$basket_items = [];

if (!empty($_SESSION['basket'])) {
    $ids = implode(',', array_keys($_SESSION['basket']));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    if ($result) {
        $products = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($products as $product) {
            $product['quantity'] = $_SESSION['basket'][$product['id']];
            $basket_items[] = $product;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket</title>
</head>
<body>
    <h1>Your Basket</h1>

<?php if (empty($basket_items)): ?>
    <p>Your basket is empty.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>

        <?php $grandTotal = 0; ?>

        <?php foreach ($basket_items as $item): ?>
            <?php
                $total = $item['price'] * $item['quantity'];
                $grandTotal += $total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>£<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>£<?php echo number_format($total, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Total: £<?php echo number_format($grandTotal, 2); ?></h3>
<?php endif; ?>
    <form method="POST">
        <input type="hidden" name="remove_id" value="<?php echo $item['id']; ?>">
        <button type="submit">Remove</button>
    </form>
</body>
</html>
