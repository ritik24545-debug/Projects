<?php
$servername = "localhost";   // XAMPP/WAMP default
$username   = "root";        // default username
$password   = "";            // default password is blank
$database   = "lost_found_db";  

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

?>
