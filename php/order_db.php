<?php
session_start();
require_once 'config.php'

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    order_type VARCHAR(20) NOT NULL,      -- 'delivery' or 'collection'
    order_status VARCHAR(50) NOT NULL,    -- 'Pending', 'Preparing', etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);