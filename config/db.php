<?php
$servername = "tcp:suthipong.database.windows.net,1433"; // คัดลอกมาจาก Azure Portal
$username = "educonn"; // คัดลอกมาจาก Azure Portal (ต้องมี @servername)
$password = "@Suthipong555"; // รหัสผ่านที่คุณตั้งไว้
$dbname = "edu e-service"; // ชื่อฐานข้อมูลที่คุณสร้างไว้ใน MySQL Server

// สำหรับ Azure Database for MySQL Flexible Server มักจะต้องใช้ SSL
// คุณอาจจะต้องดาวน์โหลด SSL certificate (BaltimoreCyberTrustRoot.crt.pem)
// และระบุ path ในโค้ดด้วย
// $ssl_ca = "/path/to/your/cert/BaltimoreCyberTrustRoot.crt.pem"; // เปลี่ยน path ให้ถูกต้อง

// สร้างการเชื่อมต่อ
$conn = mysqli_init();
// หากต้องการใช้ SSL (แนะนำสำหรับ Azure Database for MySQL)
// mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);
$conn_success = mysqli_real_connect($conn, $servername, $username, $password, $dbname, 3306, MYSQLI_CLIENT_SSL); // ถ้าไม่ใช้ SSL ให้ลบ MYSQLI_CLIENT_SSL ออก

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