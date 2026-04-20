 <?php
session_start();
require_once 'config.php';

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
</head>
<body>
    <div class="container">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>
            <br>
            <a href="homepage.php">Back to Homepage</a>
        </div>
        
        <div class="sidebar">
            <nav>
                <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
                <li><a href="profile.php">Profile</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="admin_products.php">Categories</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="delivery_collection.php">Delivery and Collection</a></li>
                <li><a href="GLHLoyalty.php">GLHLoyalty</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" id="logout-link">Logout</a></li>
            </nav>
        </div>
    </div>
</body>
</html>