<?php
require_once("../config/db.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // ใช้ password เริ่มต้น

// INSERT ข้อมูลนิสิต
$sql = "INSERT INTO students (name, email, password) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $password]);

// ดึง ID ล่าสุด
$last_id = $pdo->lastInsertId();
$x_part = str_pad($last_id, 5, '0', STR_PAD_LEFT);  
$student_code = "674" . $x_part . "27";

// อัปเดตรหัสนิสิต
$sql = "UPDATE students SET student_code = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_code, $last_id]);

// Redirect ไปยังหน้า set_password.php โดยส่ง student_code ไปด้วย
header("Location: ../Public/set_password.php?student_code=$student_code");
exit;
?>
