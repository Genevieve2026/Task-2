<?php
session_start();
include 'config.php';

// create products table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(5, 2) DEFAULT 0,
    product_quantity INT DEFAULT 1, 
    image VARCHAR(255),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Farmer/Producer') {
    header('Location: index.php');
    exit();
}

// Handle add product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $details = $_POST['details'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../pictures/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category, price, discount, quantity, image, details) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddis", $name, $category, $price, $discount, $quantity, $image, $details);
    if ($stmt->execute()) {
        $message = "Product added successfully!";
        header('Location: admin_products.php');
        exit();
    } else {
        $message = "Error adding product: " . $conn->error;
    }
}

// Handle update product
if (isset($_POST['update_product'])) {
    $id = (int) $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, discount=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssddii", $name, $category, $price, $discount, $quantity, $id);
    if ($stmt->execute()) {
        $message = "Product updated successfully!";
        header('Location: admin_products.php');
        exit();
    } else {
        $message = "Error updating product: " . $conn->error;
    }
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>

    <!--font awesome link for icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!--CSS file link-->
    <link rel="stylesheet" href="../css/admin_products.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>
    <p><a href="categories.php" class="btn btn--small">View marketplace</a></p>

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="add_products">

        <h1 class="title">Add New Products</h1>

        <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="flex">
                <input type="text" name="name" placeholder="Enter product name" class="box" required>
                <input type="text" name="seller_name" placeholder="Enter seller name" class="box" required>
                <select name="category" class="box" required>
                    <option value="" disabled selected>Select category</option>
                    <option value="vegetables">Vegetables</option>
                    <option value="fruits">Fruits</option>
                    <option value="seafood">Seafood</option>
                    <option value="proteins">Proteins</option>
                    <option value="dairy">Dairy</option>
                    <option value="seasonal">Seasonal Produce</option>
                </select>
            </div>
            <div class="inputBox">
                <input type="number" min="0" step="0.01" name="price" required placeholder="Enter product price" class="box">
                <input type="number" min="0" max="100" step="0.01" name="discount" required placeholder="Enter discount percentage" class="box">
                <input type="number" name="quantity" min="1" value="1" class="box" required placeholder="Enter quantity">
                <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
            <textarea name="details" class="box" cols="30" rows="10" placeholder="Enter product details"></textarea>
            <input type="submit" class="btn" value="Add Product" name="add_product">
        </form>


    </section>

    <section class="manage_products">
        <h1 class="title">Manage Products</h1>
        <div class="product-list">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                        /*figure out how to add sellers name in here*/
                        <p>Price: £<?php echo number_format($product['price'], 2); ?></p>
                        <p>Discount: <?php echo $product['discount'] ?? 0; ?>%</p>
                        <p>Stock: <?php echo $product['quantity']; ?></p>
                    </div>
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        <select name="category" required>
                            <option value="vegetables" <?php if ($product['category'] == 'vegetables') echo 'selected'; ?>>Vegetables</option>
                            <option value="fruits" <?php if ($product['category'] == 'fruits') echo 'selected'; ?>>Fruits</option>
                            <option value="seafood" <?php if ($product['category'] == 'seafood') echo 'selected'; ?>>Seafood</option>
                            <option value="proteins" <?php if ($product['category'] == 'proteins') echo 'selected'; ?>>Proteins</option>
                            <option value="dairy" <?php if ($product['category'] == 'dairy') echo 'selected'; ?>>Dairy</option>
                            <option value="seasonal" <?php if ($product['category'] == 'seasonal') echo 'selected'; ?>>Seasonal Produce</option>
                        </select>
                        <input type="number" min="0" step="0.01" name="price" value="<?php echo $product['price']; ?>" class="box" required>
                        <input type="number" min="0" max="100" step="0.01" name="discount" value="<?php echo $product['discount'] ?? 0; ?>" class="box" required>
                        <button type="submit" name="update_product" class="btn">Update</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

</body>
</html>