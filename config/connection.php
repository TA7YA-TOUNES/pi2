<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "pi";

// Create connection
$connexion = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$connexion) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
