<?php
$servername = "localhost"; // Default for XAMPP
$username = "root";        // Default XAMPP MySQL user
$password = "";            // Default XAMPP password (blank by default)
$dbname = "computer_lab_monitoring_system"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
