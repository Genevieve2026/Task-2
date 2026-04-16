<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'glh_db');

// Connect to MySQL server (no DB yet)
$conn = new mysqli('127.0.0.1', 'root', '');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-create database if missing, then select it
if (!$conn->query("CREATE DATABASE IF NOT EXISTS glh_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
    die("Database creation failed: " . $conn->error);
}
if (!$conn->select_db('glh_db')) {
    die("Database selection failed: " . $conn->error);
}


// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
