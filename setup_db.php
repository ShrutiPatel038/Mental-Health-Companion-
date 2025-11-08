<?php
// setup_db.php - Run this once to create the database and tables

$host = 'localhost';
$username = 'root';
$password = 'pookie2102';

$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = file_get_contents('init_db.sql');
if ($conn->multi_query($sql)) {
    echo "Database and tables created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>