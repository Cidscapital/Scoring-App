<?php
// Include the configuration file
include 'config.php';

// Establish the database connection using MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");
?>