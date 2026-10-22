<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    die("Database connection failed.");
}
$conn->set_charset("utf8mb4");
?>