<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Create orders table if not exists
$createOrders = "
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_number VARCHAR(50) NOT NULL UNIQUE,
  status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  total DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!$conn->query($createOrders)) {
    die("Table create failed: " . $conn->error);
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
}
$query = $conn->prepare("SELECT id, order_number, status, total, created_at, updated_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
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
        <h1>My Orders</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</p>

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

        <header>
        <nav>
            <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
        </nav>
    
    </header>
    <div class="sidebar">
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="marketplace.php">Marketplace</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="delivery_collection.php">Delivery and Collection</a></li>
            <li><a href="GLHLoyalty.php">GLHLoyalty</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <section class="hero">
            <h1>Welcome to Greenfield Local Hub</h1>
            <h1>Find local farmers and producers near you</h1>
            <form>
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" placeholder="What are you looking for? Seasonal items...? Locations...?" name="search">
                </div>
            </form>
            
        </section>

        <div class="card-container">
        <div class="card">
            <img src="../pictures/fruit&veg.jpg" alt="What Greenfield Local Hub Offers">
            <h3>FOOD</h3>
            <p>Discover a wide variety of fresh and locally sourced fruits and vegetables, perfect for your healthy meals.</p>
        </div>

        <section class="features">
            <div class="feature">
                <h3>FOOD, DRINKS AND FRESH PRODUCE</h3>
                <p>We offer a wide range of healthcare services to meet your needs, from primary care to specialized treatments.</p>
            </div>
            <div class="feature">
                <h3>COME CHECK IT OUT!</h3>
                <p>Our team consists of highly skilled and compassionate healthcare providers who are dedicated to your well-being.</p>
            </div>
            <div class="feature">
                <h3>JOIN OUR COMMUNITY</h3>
                <p>Our modern facilities are equipped with the latest technology to ensure you receive the best care possible.</p>
            </div>
            <div class="feature">
                <h3>HAVE A LOOK AT WHATS NEW</h3>
                <p>Our modern facilities are equipped with the latest technology to ensure you receive the best care possible.</p>
            </div>
        </section>
    </div>


        <br>
        <a href="homepage.php">Back to Homepage</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
