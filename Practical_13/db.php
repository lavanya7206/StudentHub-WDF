<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    die("Database connection failed.");
}

// Set character encoding
$conn->set_charset("utf8mb4");
?>