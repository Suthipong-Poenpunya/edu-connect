<?php
$servername = "localhost";
$username = "root";
$password = "@Suthipong555";
$dbname = "edu e-service";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
