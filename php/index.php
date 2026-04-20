<?php
session_start();
require_once 'config.php';


// create users table if not exists
$createUsers = "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('Customer','Farmer/Producer') NOT NULL,
  total_points INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!$conn->query($createUsers)) {
    die("Table create failed: " . $conn->error);
}



$message = '';
$messageType = ''; // 'success' or 'error'

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // LOGIN HANDLER
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = trim($_POST['login-email'] ?? '');
        $password = $_POST['login-password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $message = 'Please fill in all fields';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email format';
            $messageType = 'error';
        } else {
            // Check database
            $query = $conn->prepare("SELECT id, first_name, last_name, password, role FROM users WHERE email = ?");
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Login successful - set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect based on role
                    if ($user['role'] === 'Customer') {
                        header('Location: users.php');
                        exit;
                    } elseif ($user['role'] === 'Farmer/Producer') {
                        header('Location: admin.php');
                        exit;
                    } else {
                        $message = 'Unknown role. Please contact support.';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid password';
                    $messageType = 'error';
                }
            } else {
                $message = 'Email not found';
                $messageType = 'error';
            }
            
            $query->close();
        }
    }
    
    // REGISTER HANDLER
    elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
        $first_name = trim($_POST['signup-first-name'] ?? '');
        $last_name = trim($_POST['signup-last-name'] ?? '');
        $email = trim($_POST['signup-email'] ?? '');
        $password = $_POST['signup-password'] ?? '';
        $confirm_password = $_POST['signup-confirm-password'] ?? '';
        $role = trim($_POST['signup-role'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
            $message = 'All fields are required';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email format';
            $messageType = 'error';
        } elseif ($password !== $confirm_password) {
            $message = 'Passwords do not match';
            $messageType = 'error';
        } elseif (strlen($password) < 6) {
            $message = 'Password must be at least 6 characters long';
            $messageType = 'error';
        } else {
            // Check if email already exists
            $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();
            
            if ($result->num_rows > 0) {
                $message = 'Email already registered';
                $messageType = 'error';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user into database
                $insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $insert->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);
                
                if ($insert->execute()) {
                    $message = 'Registration successful! You can now login.';
                    $messageType = 'success';
                } else {
                    $message = 'Registration failed. Please try again.';
                    $messageType = 'error';
                }
                
                $insert->close();
            }
            
            $check_email->close();
        }
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!--linking style sheet and script file to the html page--->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<!--creating Login and Sign up toggles-->
<body>
    <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
    <div class="container">
        <div class="form-container">
            
            <!-- Message Display -->
            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Show logged in info or forms -->
            <?php if ($isLoggedIn): ?>
                <div class="logged-in-container">
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
                    <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>



                    <form method="POST" action="../php/logout.php">
                        <button type="submit" class="btn">Logout</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="form-toggle">
                    <button type="button" id="login-toggle" class="active">Login</button>
                    <button type="button" id="signup-toggle">Register with Us!</button>
                </div>
                
                <!--creating login and sign up forms--->
                <form id="login-form" class="form active" method="POST" action="">
                    <h2>Login</h2>
                    <div class="input-group">
                        <label for="login-email">Email</label>
                        <input type="email" name="login-email" id="login-email" required>
                    </div>
                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <input type="password" name="login-password" id="login-password" required>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <button type="submit" class="btn">Login</button>
                    <p class="switch-link">Don't have an account? <a href="#" id="switch-to-signup">Sign up</a></p>
                </form>
                
                <form id="signup-form" class="form" method="POST" action="">
                    <h2>Register with Us!</h2>
                    <div class="input-group">
                        <label for="signup-first-name">First Name</label>
                        <input type="text" name="signup-first-name" id="signup-first-name" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-last-name">Last Name</label>
                        <input type="text" name="signup-last-name" id="signup-last-name" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-email">Email</label>
                        <input type="email" name="signup-email" id="signup-email" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-password">Password</label>
                        <input type="password" name="signup-password" id="signup-password" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-confirm-password">Confirm Password</label>
                        <input type="password" name="signup-confirm-password" id="signup-confirm-password" required>
                    </div>
                    <!-- giving users the ability to choose their role -->
                    <div class="input-group">
                        <label for="signup-role">Select Role</label>
                        <select name="signup-role" id="signup-role" class="role" required>
                            <option value="">Select Role</option>
                            <option value="Customer">Customer</option>
                            <option value="Farmer/Producer">Farmer/Producer</option>
                        </select>
                    </div>
                    <input type="hidden" name="action" value="register">
                    <button type="submit" class="btn">Sign Up</button>
                    <p class="switch-link">Already have an account? <a href="#" id="switch-to-login">Sign in!</a></p>
                </form>
            <?php endif; ?>
            
        </div>
        <a href="../php/homepage.php" class="back-button">
                <span class="back-icon">&#8592;</span> Back to Homepage
            </a>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
