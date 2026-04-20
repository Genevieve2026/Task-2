<?php
echo "Starting test\n";
require 'config.php';
echo "Config loaded\n";
if ($conn->connect_error) {
    echo 'Error: ' . $conn->connect_error . "\n";
} else {
    echo 'Connected successfully' . "\n";
}
?>