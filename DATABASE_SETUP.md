# Database Setup Instructions

## MySQL Database Setup

### 1. Create Database
```sql
CREATE DATABASE IF NOT EXISTS login_system;
USE login_system;
```

### 2. Create Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Customer', 'Farmer/Producer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Database Connection Details
- **Host:** localhost
- **Username:** root
- **Password:** (set your own or leave blank)
- **Database Name:** login_system

## Configuration
Update `config.php` with your database credentials if different from the defaults.
