<?php
include ('/home/site/wwwroot/config/db.php');

if (!isset($conn)) {
    die("Database connection error.");
}
if ($conn) {
    echo "Database connected successfully.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_code = $_POST['student_code'];
    $password = $_POST['password'];

    $query = "SELECT * FROM students WHERE student_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start(); // **ตรวจสอบให้แน่ใจว่า session_start() อยู่ที่นี่**
            $_SESSION['student_code'] = $user['student_code']; // เปลี่ยนเป็น 'student_code'
            $_SESSION['user_id'] = $user['id']; // เก็บ user_id ด้วย
            header("Location: /Public/dashboard.php");
            exit();
        } else {
            echo "Incorrect password";
            header("Location: /Public/login.php?error=Incorrect password");
        }
    } else {
        echo "User not found";
    }

    $stmt->close();
    $conn->close();
}
?>