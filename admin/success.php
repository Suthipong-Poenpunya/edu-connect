<?php
// Start session if needed
session_start();

// Get the previous page URL
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';

// Set a 3-second delay before redirecting
header("Refresh: 3; url=$previousPage");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 20%;
        }
    </style>
</head>
<body>
    <h1>บันทึกข้อมูลสำเร็จ!</h1>
    <p>กำลังนำคุณกลับไปยังหน้าก่อนหน้าใน 3 วินาที...</p>
</body>
</html>