<?php
$conn = new mysqli("localhost", "root", "", "studenthub");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    exit("Database connection failed. Please try again later.");
}
$conn->set_charset("utf8mb4");
?>