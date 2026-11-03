
<?php
$host = "sql300.infinityfree.com";
$username = "if0_43083347";
$password = "N8QPh1aU4Z71vAS";
$database = "if0_43083347_mira_artcraft";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>
