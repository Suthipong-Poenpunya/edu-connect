<?php
$con = mysqli_init();
mysqli_ssl_set($con,NULL,NULL, "{path to CA cert}", NULL, NULL);
mysqli_real_connect($conn, "edu-cuconnect-server.mysql.database.azure.com", "gdpgpjwnrd", "@Suthipong555", "edu e-service", 3306, MYSQLI_CLIENT_SSL);
// สำหรับ Azure Database for MySQL Flexible Server มักจะต้องใช้ SSL
// คุณอาจจะต้องดาวน์โหลด SSL certificate (BaltimoreCyberTrustRoot.crt.pem)
// และระบุ path ในโค้ดด้วย
// $ssl_ca = "/path/to/your/cert/BaltimoreCyberTrustRoot.crt.pem"; // เปลี่ยน path ให้ถูกต้อง
$host = "edu-cuconnect-server.mysql.database.azure.com"; // ชื่อโฮสต์ของ Azure MySQL
$username = "gdpgpjwnrd"; // ชื่อผู้ใช้ 
$password = "@Suthipong555"; // รหัสผ่าน
$database = "edu e-service"; // ชื่อฐานข้อมูล
if (!$conn_success) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully to Azure MySQL";

// ... โค้ด PHP ของคุณที่ใช้งาน $conn ...

// ปิดการเชื่อมต่อเมื่อใช้งานเสร็จ
// mysqli_close($conn);
// ตั้งค่า charset เป็น utf8mb4 เพื่อรองรับภาษาไทยและอีโมจิ
$conn->set_charset("utf8mb4");
// ปิดการเชื่อมต่อเมื่อใช้งานเสร็จ
?>