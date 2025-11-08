<?php
// db.php
function get_db() {
    $host = 'localhost';
    $dbname = 'mindful_moments';
    $username = 'root';
    $password = '1234';
    
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>