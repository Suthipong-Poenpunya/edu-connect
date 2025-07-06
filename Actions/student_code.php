<?php
require_once("../config/db.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// เพิ่มข้อมูลนักศึกษาใหม่
$sql = "INSERT INTO students (name, email, password) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $password]);

// สร้างรหัสนิสิตใหม่
$last_id = $pdo->lastInsertId();
$x_part = str_pad($last_id, 5, '0', STR_PAD_LEFT);  // เติม 0 ให้ครบ 5 หลัก
$student_code = "674" . $x_part . "27";

// อัปเดตรหัสนิสิตใน record นั้น
$sql = "UPDATE students SET student_code = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_code, $last_id]);

// แสดงผลให้ผู้ใช้เห็น
echo "สมัครเรียบร้อย! รหัสนิสิตของคุณคือ <b>$student_code</b>";
?>
