<?php
include('../config/db.php');

if (isset($_POST['student_code']) && isset($_POST['new_password'])) {
    $student_code = $_POST['student_code'];
    $new_password = $_POST['new_password'];

    // Hash รหัสผ่านใหม่
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // ใช้ mysqli เตรียมคำสั่ง SQL และ bind parameter
    $sql = "UPDATE students SET password = ? WHERE student_code = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $student_code);

    if ($stmt->execute()) {
        // ถ้าบันทึกสำเร็จ
        header("Location: ../Actions/success.php?id=$id");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึก: " . $stmt->error;
    }
} else {
    echo "ข้อมูลไม่ครบถ้วน";
}
?>
