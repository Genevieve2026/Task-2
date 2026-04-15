<?php
session_start();
include 'config.php';

//hardcoded products for category cards - there are also dynamic products from the database in the "explore" section below.
//the dynamic products are added through the admin panel

// Fetch all products from database
$products = [
    // Example hardcoded product

    [
        'id' => 1,
        'name' => 'Broccoli',
        'details' => 'Fresh, locally-sourced broccoli packed with nutrients and flavor.',
        'category' => 'Vegetables',
        'price' => 2.99,
        'discount' => 10,
        'image' => '../images/example-product.jpg'
    ]
];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLH Categories</title>
    <link rel="stylesheet" href="../css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <section class="categories" id="categories">
        <div class="categories__hero">
            <div class="categories__heading">
                <span class="eyebrow">categories</span>
                <span class="back-link"><a href="../php/homepage.php"><i class="fas fa-arrow-left"></i> Back to Home</a></span>
                <h1>Browse Our Categories</h1>

                <p>Discover a wide range of locally sourced products from our trusted farmers and producers.</p>
            </div>
        </div>

        <div class="categories__cards">
            <article class="category-card">
                <div class="category-card__title">Animal Protein</div>
                <p>This category features a variety of high-quality animal proteins sourced from local farms.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #d44f4f;"></span>
                    <span class="category-chip" style="--chip-color: #016601;">Halal Available</span>
                    <span class="category-chip" style="--chip-color: #111184;">Kosher Available</span>
                </div>
            </article>

            <article class="category-card">
                <div class="category-card__title">Fresh Poultry</div>
                <p>This category features a variety of fresh, locally-sourced poultry.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #dae18d;"></span>
                    <span class="category-chip" style="--chip-color: #016601;">Halal Available</span>
                    <span class="category-chip" style="--chip-color: #111184;">Kosher Available</span>
                </div>
            </article>

            <article class="category-card">
                <div class="category-card__title">Seafood</div>
                <p>This category features a variety of fresh seafood sourced from local fisheries.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #4bc2a3;"></span>
                    <span class="category-chip" style="--chip-color: #c24b4b;">Crustaceans</span>
                    <span class="category-chip" style="--chip-color: #840000;">Shellfish</span>
                </div>
            </article>

            <article class="category-card">
                <div class="category-card__title">Fresh Produce</div>
                <p>This category features a variety of fresh, locally-sourced fruits and vegetables.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #62a93b;"></span>
                    <span class="category-chip" style="--chip-color: #f2a33c;">Fruits</span>
                    <span class="category-chip" style="--chip-color: #308800;">Vegetables</span>
                </div>
            </article>

            <article class="category-card">
                <div class="category-card__title">Seasonal Specials</div>
                <p>This category features seasonal products that are available for a limited time.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #ff8c42;"></span>
                    <span class="category-chip" style="--chip-color: #e5005f;">Limited Time</span>
                </div>
            </article>


            <article class="category-card">
                <div class="category-card__title">Dairy Products</div>
                <p>This category features a variety of fresh, locally-sourced dairy products.</p>
                <div class="category-card__footer">
                    <span class="category-chip" style="--chip-color: #7b6cca;"></span>
                    <span class="category-chip" style="--chip-color: #9981a0;">Milk</span>
                    <span class="category-chip" style="--chip-color: #fffc66;">Cheese</span>
                </div>
            </article>

        </div>

        <div class="categories__explore">
            <div class="categories__heading">
                <span class="eyebrow">explore</span>
                <h1>Explore Our Marketplace</h1>
                <p>All products added by admins appear here automatically.</p>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Farmer/Producer'): ?>
                    <div class="admin-button">
                        <a href="admin_products.php" class="btn btn--small">Manage products</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="categories__cards">
                <?php if (empty($products)): ?>
                    <article class="category-card no-products">
                        <div class="category-card__title">No products available</div>
                        <p>Products will appear here once an admin adds them.</p>
                    </article>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $categoryColors = [
                            'vegetables' => '#62a93b',
                            'fruits' => '#f2a33c',
                            'seafood' => '#4bc2a3',
                            'proteins' => '#d44f4f',
                            'dairy' => '#7b6cca',
                            'seasonal' => '#ff8c42'
                        ];
                        $chipColor = $categoryColors[strtolower($product['category'])] ?? '#999999';
                        $price = number_format($product['price'], 2);
                        $discount = floatval($product['discount']);
                        $discountedPrice = $discount > 0 ? number_format($product['price'] * (1 - $discount / 100), 2) : null;
                        ?>
                        <article class="category-card">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="category-card__image">
                            <div class="category-card__title"><?php echo htmlspecialchars($product['name']); ?></div>
                            <p><?php echo htmlspecialchars($product['details']); ?></p>
                            <div class="category-card__footer">
                                <span class="category-chip" style="--chip-color: <?php echo $chipColor; ?>; "><?php echo htmlspecialchars($product['category']); ?></span>
                                <?php if ($discount > 0): ?>
                                    <span class="category-chip" style="--chip-color: #a10000;">-<?php echo $discount; ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="category-card__price">
                                <?php if ($discountedPrice !== null): ?>
                                    <span class="original-price">£<?php echo $price; ?></span>
                                    <span class="discount-price">£<?php echo $discountedPrice; ?></span>
                                <?php else: ?>
                                    <span class="price">£<?php echo $price; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="category-card__actions">
                                <button type="button" class="btn btn--small"><a href="../php/index.php">Add to basket</a></button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="page-footer">
        <p>&copy; 2026 GLH. All rights reserved.</p>
    </footer>
</body>
</html>