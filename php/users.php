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

// Fetch user's current points with error handling
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

// Points calculation constant (10 points per £1 spent)
$POINTS_PER_POUND = 10;

// Create orders table if not exists
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
if (!$conn->query($createOrders)) {
    die("Table create failed: " . $conn->error);
}

// Ensure points_earned column exists in orders table
$resultOrders = $conn->query("SHOW COLUMNS FROM orders LIKE 'points_earned'");
if ($resultOrders->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN points_earned INT DEFAULT 0");
}

// Create point history table if not exists
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
if (!$conn->query($createPointHistory)) {
    die("Point history table create failed: " . $conn->error);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Allow user to cancel their own pending orders
    if ($new_status === 'Cancelled') {
        $updateQuery = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status = 'Pending'");
        $updateQuery->bind_param("ii", $order_id, $user_id);
        $updateQuery->execute();
        $updateQuery->close();
    }
    
    // Award points when order is delivered
    if ($new_status === 'Delivered') {
        $orderQuery = $conn->prepare("SELECT order_number, total, points_earned, status FROM orders WHERE id = ? AND user_id = ?");
        $orderQuery->bind_param("ii", $order_id, $user_id);
        $orderQuery->execute();
        $orderResult = $orderQuery->get_result();
        $orderData = $orderResult->fetch_assoc();
        $orderQuery->close();
        
        if ($orderData && $orderData['points_earned'] == 0) {
            // Calculate points (10 points per £1)
            $pointsEarned = (int)($orderData['total'] * $POINTS_PER_POUND);
            
            // Update order with points earned
            $updateOrder = $conn->prepare("UPDATE orders SET points_earned = ? WHERE id = ?");
            $updateOrder->bind_param("ii", $pointsEarned, $order_id);
            $updateOrder->execute();
            $updateOrder->close();
            
            // Add to user's total points
            $updateUser = $conn->prepare("UPDATE users SET total_points = total_points + ? WHERE id = ?");
            $updateUser->bind_param("ii", $pointsEarned, $user_id);
            $updateUser->execute();
            $updateUser->close();
            
            // Log point history
            $historyInsert = $conn->prepare("INSERT INTO point_history (user_id, order_id, points_earned, description) VALUES (?, ?, ?, ?)");
            $description = "Earned from order #" . $orderData['order_number'];
            $historyInsert->bind_param("iis", $user_id, $order_id, $pointsEarned, $description);
            $historyInsert->execute();
            $historyInsert->close();
        }
    }
}
$query = $conn->prepare("SELECT id, order_number, status, total, points_earned, created_at, updated_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$query->close();
// Determine latest active order for stepper
$latestOrder = !empty($orders) ? $orders[0] : null;
$statusStepMap = [
    'Pending' => 1,
    'Processing' => 2,
    'Shipped' => 3,
    'Delivered' => 4,
    'Cancelled' => 0,
];
$currentStep = $latestOrder ? ($statusStepMap[$latestOrder['status']] ?? 0) : 0;

function get_step_class($step, $currentStep, $status) {
    if ($status === 'Cancelled') {
        return 'step disabled';
    }
    if ($step < $currentStep) {
        return 'step completed';
    }
    if ($step === $currentStep) {
        return 'step active';
    }
    return 'step';
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLH User Dashboard</title>
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>
        
        
        <!-- Points Tracker Card -->
        <div class="points-tracker-card">
            <div class="points-content">
                <div class="points-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="points-info">
                    <h3>GLH Loyalty Points</h3>
                    <p class="points-display"><?php echo number_format($userPoints); ?> Points</p>
                    <p class="points-description">Earn 10 points for every £1 spent. Redeem for rewards!</p>
                    <a href="GLHLoyalty.php" class="points-link">View Rewards →</a>
                </div>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <p>You have no orders yet.</p>
        <?php else: ?>
            <?php if ($latestOrder): ?>
            <div class="order-stepper-wrapper">
                <h2>Track your order: <span><?php echo htmlspecialchars($latestOrder['order_number']); ?></span></h2>
                <div class="order-stepper">
                    <div class="<?php echo get_step_class(1, $currentStep, $latestOrder['status']); ?>">
                        <div class="step-circle"><i class="fas fa-clipboard-list"></i></div>
                        <div class="step-label">Placed</div>
                    </div>
                    <div class="<?php echo get_step_class(2, $currentStep, $latestOrder['status']); ?>">
                        <div class="step-circle"><i class="fas fa-users"></i></div>
                        <div class="step-label">Processing</div>
                    </div>
                    <div class="<?php echo get_step_class(3, $currentStep, $latestOrder['status']); ?>">
                        <div class="step-circle"><i class="fas fa-truck"></i></div>
                        <div class="step-label">Shipped</div>
                    </div>
                    <div class="<?php echo get_step_class(4, $currentStep, $latestOrder['status']); ?>">
                        <div class="step-circle"><i class="fas fa-home"></i></div>
                        <div class="step-label">Delivered</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Points Earned</th>
                        <th>Created At</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>£<?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo $order['points_earned'] > 0 ? '+' . number_format($order['points_earned']) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($order['updated_at']); ?></td>
                            <td>
                                <?php if ($order['status'] === 'Pending'): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="new_status" value="Cancelled">
                                        <button type="submit">Cancel Order</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <br>
        <a href="homepage.php">Back to Homepage</a>
    </div>
    
    <div class="sidebar">
        <nav>
            <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
            <li><a href="profile.php">Profile</a></li>
            <li><a href="categories.php">Categories and Produce</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="delivery_collection.php">Delivery and Collection</a></li>
            <li><a href="GLHLoyalty.php">GLHLoyalty</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php" id="logout-link">Logout</a></li>
        </nav>
    </div>

  
</body>
</html>

<?php
$conn->close();
?>
