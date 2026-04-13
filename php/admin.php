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
            <li><a href="marketplace.php">Marketplace</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="delivery_collection.php">Delivery and Collection</a></li>
            <li><a href="GLHLoyalty.php">GLHLoyalty</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php" id="logout-link">Logout</a></li>
        </nav>
    </div>
