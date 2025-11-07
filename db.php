<?php
// db.php
require 'vendor/autoload.php'; // Include Composer's autoloader

function get_db() {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    return $client->mindful_moments; // Selects the 'mindful_moments' database
}
?>