<?php
session_start();

// ลบ session ID เก่า และสร้าง session ID ใหม่
session_regenerate_id(true);

// ลบตัวแปรทั้งหมดใน session
session_unset();

// ทำลาย session
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้าล็อกอิน
header("Location: ../Public/index.php");
exit();
?>