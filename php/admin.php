 <?php
session_start();
require_once 'config.php';


// create sales table if no exists
$sql = "CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100),
    amount DECIMAL(10,2)
)";
$conn->query($sql);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLH Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"rel="stylesheet">
</head>
<body>
    <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>

            <div class="sidebar">
                <nav>
                    <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
                    <li><i class="fa-solid fa-user"></i><a href="profile.php">Profile</a></li>
                    <li><i class="fa-solid fa-shop"></i><a href="admin_categories.php">Marketplace</a></li>
                    <li><i class="fa-solid fa-list"></i><a href="admin_products.php">Categories</a></li>
                    <li><i class="fa-solid fa-file-alt"></i><a href="../php/order_confirmation.php">My Orders</a></li>
                    <li><i class="fa-solid fa-truck-fast"></i><a href="delivery_collection.php">Delivery and Collection</a></li>
                    <li><i class="fa-solid fa-hand-holding-heart"></i><a href="GLHLoyalty.php">GLHLoyalty</a></li>
                    <li><i class="fa-solid fa-cog"></i><a href="settings.php">Settings</a></li>
                    <li><i class="fa-solid fa-sign-out"></i><a href="logout.php" id="logout-link">Logout</a></li>
                </nav>
            </div>

            <div class="main-content">
                <h2>Sales Overview</h2>
                <div class="sales-overview">
                    <div class="sales-summary">
                        <h2>Total Sales:</h2>
                        <p>£<?php echo number_format(5000, 2); ?></p>
                        <h2>Monthly Growth:</h2><p> 10%</p>
                        <h2>Top Category:</h2><p> Fruits</p>
                    </div>

                    <div class="sales-overview">
                    <div class="sales-summary">
                        <h2>Need some new marketing strategies?</h2>
                        <p>Check out <a href="marketing.php">GLH's Marketing Insight!</a></p>
                    </div>

            <div class="sales_cards">
            <article class="sales-card">
                <div class="sales-card__title">Monthly Category</div>
                <p>This category features the best category of yours for the month.</p>
                </div>
            </article>

            <div class="sales_pie_cards">
            <article class="sales-pie-card">
                 <canvas id="salesPieChart" width="10" height="10"></canvas>

            
            <div class="sales-pie-card__header">
                <h3 class="sales-pie-card__title">Sales Overview</h3>
                <p>Here's a summary of your sales performance.</p>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="../js/sales.js"></script>

            </article>
            </div>
        </div>
    </div>
</body>
</html>