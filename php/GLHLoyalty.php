<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure total_points column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'total_points'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN total_points INT DEFAULT 0");
}

// Create point history table if it doesn't exist
$createPointHistory = "
CREATE TABLE IF NOT EXISTS point_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_id INT,
  points_earned INT NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($createPointHistory);

// Fetch user's current points
$userPoints = 0;
$userQuery = $conn->prepare("SELECT total_points FROM users WHERE id = ?");
if ($userQuery) {
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userData = $userResult->fetch_assoc();
    $userPoints = $userData['total_points'] ?? 0;
    $userQuery->close();
}

// Fetch point history with error handling
$pointHistory = [];
$historyQuery = $conn->prepare("SELECT points_earned, description, created_at FROM point_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
if ($historyQuery) {
    $historyQuery->bind_param("i", $user_id);
    $historyQuery->execute();
    $historyResult = $historyQuery->get_result();
    $pointHistory = $historyResult->fetch_all(MYSQLI_ASSOC);
    $historyQuery->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLHLoyalty</title>
    <link rel="stylesheet" href="../css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
        <button class="back-button" onclick="window.location.href='../php/users.php'"><i class="fas fa-arrow-left"></i> Back to Account</button>
        <h1>GLH Loyalty Program</h1>
        <p>Welcome to the GLH Loyalty Program! Earn points with every purchase and redeem them for exciting rewards.</p>
        <div class="loyalty-info">
            <h2>Your Points: <span style="color: #2c8d45;"><?php echo number_format($userPoints); ?></span></h2>
            <p>Points are earned based on your purchases. For every £1 spent, you earn 10 points.</p>
            <p>Redeem your points for discounts, free products, and exclusive offers!</p>
        </div>
    </div>

    <?php if (!empty($pointHistory)): ?>
    <div class="container" style="margin-top: 30px;">
        <h2>Recent Point Activity</h2>
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #2c8d45; color: white;">
                    <th style="padding: 10px;">Points</th>
                    <th style="padding: 10px;">Description</th>
                    <th style="padding: 10px;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pointHistory as $entry): ?>
                <tr>
                    <td style="padding: 10px; font-weight: bold; color: #2c8d45;">+<?php echo number_format($entry['points_earned']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($entry['description']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($entry['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="card-container">
        <div class="card">
            <h2>What is GLHLoyalty?</h2>
            <p>GLHLoyalty is a way of us at Greenfield Local Hub to reward and give back to our new and returning customers.</p>    
        </div>
        <div class="card">
            <h2>How to Earn Points</h2>
            <p>Earn points by making purchases on our platform. The more you shop, the more points you earn!</p>
        </div>
        <div class="card">
            <h2>How to Redeem Points</h2>
            <p>Redeem your points at checkout to get discounts on your orders. </p>
        </div>
        <div class="card">
            <h2>Perks, Deals, Discounts and Exclusive Offers</h2>
            <p>Use your points to access exclusive rewards, such as early access to sales, special discounts, and free products.</p>
        </div>
        <div class="card">
            <h2>How do I Join GLHLoyalty?</h2>
            <p>Joining GLHLoyalty is easy! Simply create an account on our website and start earning points with your purchases. Your points will be automatically tracked in your account dashboard.</p>
        </div>
    </div>


    <div class="rewards-section">
        <h2>Available Rewards</h2>
        <div class="reward-card">
            <h3>5% Off Your Next Purchase</h3>
            <p>Redeem 500 points to get a 5% discount on your next order.</p>
        </div>
        <div class="reward-card">
            <h3>Free Delivery</h3>
            <p>Redeem 1000 points to enjoy free delivery on your next order.</p>
        </div>
        <div class="reward-card">
            <h3>Exclusive Product Access</h3>
            <p>Redeem 2000 points for early access to new products and special promotions.</p>
        </div>
</body>
</html>