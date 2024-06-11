<?php

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$dbHost = getenv('DB_HOST') ?: 'localhost'; // Change this to your database host
$dbUsername = getenv('DB_USERNAME') ?: 'root'; // Change this to your database username
$dbPassword = getenv('DB_PASSWORD') ?: ''; // Change this to your database password
$dbName = getenv('DB_NAME') ?: 'loan_management_system'; // Change this to your database name

// Establish database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set session timeout to 10 minutes
ini_set('session.gc_maxlifetime', 600);
session_set_cookie_params(600);
session_start();

?>
