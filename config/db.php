<?php
// กำหนดค่าการเชื่อมต่อ
$host = "edu-cuconnect-server.mysql.database.azure.com"; // ชื่อโฮสต์ของ Azure MySQL
$username = "gdpgpjwnrd"; // ชื่อผู้ใช้ 
$password = "@Suthipong555"; // รหัสผ่าน
$database = "edu e-service"; // ชื่อฐานข้อมูล


// สร้าง mysqli object และตั้งค่า SSL
$conn = mysqli_init();
if (!$conn) {
    die("mysqli_init failed");
}

// เชื่อมต่อฐานข้อมูล
if (!mysqli_real_connect($conn, $host, $username, $password, $database, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully to Azure MySQL";

// ตั้งค่า charset เป็น utf8mb4 เพื่อรองรับภาษาไทยและอีโมจิ
$conn->set_charset("utf8mb4");

// ... โค้ด PHP ของคุณที่ใช้งาน $conn ...

// ปิดการเชื่อมต่อเมื่อใช้งานเสร็จ
// mysqli_close($conn);
?>