<?php
session_start();

// Display session data
echo "<h2>Session Data:</h2>";
var_dump($_SESSION);
var_dump($_SESSION['id']);
var_dump($_SESSION['name']);
var_dump($_POST['id']);

include '../config/db.php'; // Include your database connection file    

// Example query to fetch data from a table
$sql = "SELECT * FROM users WHERE id = ?"; // Use prepared statements to prevent SQL injection
$stmt = $conn->prepare($sql);
$result = $conn->query($sql);

echo "<h2>Database Data:</h2>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        var_dump($_POST['id']);
    }
} else {
    echo "No data found in the database.";
}

$conn->close();