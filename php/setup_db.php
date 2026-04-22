<?php
/* create all required database tables*/

include 'config.php';

// Create Users table if it doesn't exist
$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Customer', 'Farmer/Producer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($users_sql)) {
    die("Error creating users table: " . $conn->error);
}

// Create Products table if it doesn't exist
$products_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(5,2) DEFAULT 0,
    image VARCHAR(255),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($products_sql)) {
    die("Error creating products table: " . $conn->error);
}

echo "<h2 style='color: green;'>✓ Database setup complete!</h2>";
echo "<p>The following tables have been created/verified:</p>";
echo "<ul>";
echo "<li>users</li>";
echo "<li>products</li>";
echo "</ul>";
echo "<p><a href='homepage.php'>Return to homepage</a></p>";

$conn->close();
?>
