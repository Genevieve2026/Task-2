<?php
session_start();
include 'config.php';

// create products table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(5,2) DEFAULT 0,
    seller_name VARCHAR(255),
    quantity INT DEFAULT 1,
    image VARCHAR(255),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
)");

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Farmer/Producer') {
    header('Location: index.php');
    exit();
}

// Handle add product
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $category = isset($_POST['category'])
    ? implode(', ', $_POST['category']) : '';
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $details = $_POST['details'];
    $quantity = $_POST['quantity'];
    $seller_name = $_POST['seller_name'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../pictures/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category, price, discount, quantity, image, details, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddisss", $name, $category, $price, $discount, $quantity, $image, $details, $seller_name);
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
    $category = isset($_POST['category'])
    ? implode(', ', $_POST['category']) : '';
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

//delete products
if (isset($_POST['delete_product'])) {
    $id = (int) $_POST['delete_id'];

    // Optional: get image path first to delete file
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product && !empty($product['image']) && file_exists($product['image'])) {
        unlink($product['image']); // deletes image file
    }

    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_products.php");
        exit();
    } else {
        $message = "Error deleting product: " . $conn->error;
    }
}
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
    <p><a href="admin_categories.php" class="btn btn--small">View marketplace</a></p>

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    
            <section class="add_products">

                <h1 class="title">Add New Products</h1>

                <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">

                    <div class="flex">
                        <input type="text" name="product_name" placeholder="Enter product name" class="box" required>
                        <input type="text" name="seller_name" placeholder="Enter seller name" class="box" required>
                    </div>

                    <div class="box">
                        <div class="categories">
                            <label><input type="checkbox" name="category[]" value="vegetables"> Vegetables</label>
                            <label><input type="checkbox" name="category[]" value="fruits"> Fruits</label>
                            <label><input type="checkbox" name="category[]" value="seafood"> Seafood</label>
                            <label><input type="checkbox" name="category[]" value="proteins"> Proteins</label>
                            <label><input type="checkbox" name="category[]" value="dairy"> Dairy</label>
                            <label><input type="checkbox" name="category[]" value="milk"> Milk</label>
                            <label><input type="checkbox" name="category[]" value="cheese"> Cheese</label>
                            <label><input type="checkbox" name="category[]" value="seasonal"> Seasonal</label>
                            <label><input type="checkbox" name="category[]" value="Halal"> Halal</label>
                            <label><input type="checkbox" name="category[]" value="Kosher"> Kosher</label>
                            <label><input type="checkbox" name="category[]" value="Crustaceans"> Crustaceans</label>
                            <label><input type="checkbox" name="category[]" value="Shellfish"> Shellfish</label>
                            <label><input type="checkbox" name="category[]" value="Limited"> Limited</label>
                        </div>
                    </div>

                    <div class="inputBox">
                        <input type="number" min="0" step="0.01" name="price" required placeholder="Enter product price" class="box">
                        <input type="number" min="0" max="100" step="0.01" name="discount" placeholder="Discount %" class="box">
                        <input type="number" name="quantity" min="1" value="1" class="box" placeholder="Quantity">
                        <input type="file" name="image" class="box">
                    </div>

                    <textarea name="details" class="box" placeholder="Enter product details"></textarea>

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

                        <div>
                            <p>Category:</p>

                            <div class="category-chips">
                                        <?php
                                            $categories = !empty($product['category'])
                                                ? explode(', ', $product['category'])
                                                : [];

                                            foreach ($categories as $cat):
                                        ?>
                                            <span class="chip <?php echo strtolower(trim($cat)); ?>">
                                                <?php echo htmlspecialchars($cat); ?>
                                            </span>
                                        <?php endforeach; ?>
                            </div>
                        </div>
                        <p>
                            Sellers Name:
                            <?php
                            $seller_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
                            ?>
                                
                        </p>
                        <p>Price: £<?php echo number_format($product['price'], 2); ?></p>
                        <p>Discount: <?php echo $product['discount'] ?? 0; ?>%</p>
                        <p>Stock: <?php echo $product['quantity']; ?></p>
                    </div>
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        <input type="text" name="seller_name" value="<?php echo htmlspecialchars($product['seller_name'] ?? 'N/A'); ?>" placeholder="Enter seller name" class="box" required>
                        <?php
$selectedCategories = !empty($product['category'])
    ? explode(', ', $product['category'])
    : [];
?>

<div class="box">
    <div class="categories">

        <label>
            <input type="checkbox" name="category[]" value="vegetables"
            <?php if (in_array('vegetables', $selectedCategories)) echo 'checked'; ?>>
            Vegetables
        </label>

        <label>
            <input type="checkbox" name="category[]" value="fruits"
            <?php if (in_array('fruits', $selectedCategories)) echo 'checked'; ?>>
            Fruits
        </label>

        <label>
            <input type="checkbox" name="category[]" value="seafood"
            <?php if (in_array('seafood', $selectedCategories)) echo 'checked'; ?>>
            Seafood
        </label>

        <label>
            <input type="checkbox" name="category[]" value="proteins"
            <?php if (in_array('proteins', $selectedCategories)) echo 'checked'; ?>>
            Proteins
        </label>

        <label>
            <input type="checkbox" name="category[]" value="dairy"
            <?php if (in_array('dairy', $selectedCategories)) echo 'checked'; ?>>
            Dairy
        </label>

        <label>
            <input type="checkbox" name="category[]" value="milk"
            <?php if (in_array('milk', $selectedCategories)) echo 'checked'; ?>>
            Milk
        </label>

        <label>
            <input type="checkbox" name="category[]" value="cheese"
            <?php if (in_array('cheese', $selectedCategories)) echo 'checked'; ?>>
            Cheese
        </label>

        <label>
            <input type="checkbox" name="category[]" value="seasonal"
            <?php if (in_array('seasonal', $selectedCategories)) echo 'checked'; ?>>
            Seasonal
        </label>

        <label>
            <input type="checkbox" name="category[]" value="Halal"
            <?php if (in_array('Halal', $selectedCategories)) echo 'checked'; ?>>
            Halal
        </label>

        <label>
            <input type="checkbox" name="category[]" value="Kosher"
            <?php if (in_array('Kosher', $selectedCategories)) echo 'checked'; ?>>
            Kosher
        </label>

        <label>
            <input type="checkbox" name="category[]" value="Crustaceans"
            <?php if (in_array('Crustaceans', $selectedCategories)) echo 'checked'; ?>>
            Crustaceans
        </label>

        <label>
            <input type="checkbox" name="category[]" value="Shellfish"
            <?php if (in_array('Shellfish', $selectedCategories)) echo 'checked'; ?>>
            Shellfish
        </label>

        <label>
            <input type="checkbox" name="category[]" value="Limited"
            <?php if (in_array('Limited', $selectedCategories)) echo 'checked'; ?>>
            Limited
        </label>

    </div>
</div>
                        <input type="number" min="0" step="0.01" name="price" value="<?php echo $product['price']; ?>" class="box" required>
                        <input type="number" min="0" max="100" step="0.01" name="discount" value="<?php echo $product['discount'] ?? 0; ?>" class="box" required>
                        <button type="submit" name="update_product" class="btn">Update</button>
                    </form>

                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

</body>
</html>