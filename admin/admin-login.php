<?php
// เริ่มต้นการทำงานของ Session เพื่อใช้ข้อความแจ้งเตือนใน Session
session_start();

// รวมไฟล์กำหนดค่าฐานข้อมูล
// ตรวจสอบให้แน่ใจว่าพาธไปยัง db.php ถูกต้องเมื่อเทียบกับตำแหน่งของไฟล์นี้
include '../config/db.php';

// หมายเหตุ: นี่คือหน้าฟอร์มสำหรับเข้าสู่ระบบของเจ้าหน้าที่/ผู้ดูแลระบบ
// การส่งฟอร์มจะถูกจัดการโดยไฟล์ admin_login_action.php
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบสำหรับเจ้าหน้าที่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
      body {
        font-family: 'Kanit', sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 10px;
      }

      .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 100%;
        max-width: 650px;
      }

      h1 {
        color:rgb(222, 92, 142);
        text-align: center;
        margin-bottom: 20px;
      }

      .form-group {
        margin-bottom: 20px;
      }

      label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
      }

      .form-control {
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 10px;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
      }

      .form-control:focus {
        border-color:rgb(222, 92, 142);
        outline: none;
      }

      .btn-success {
        background-color:rgb(222, 92, 142);
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1em;
        width: 100%;
        transition: background-color 0.3s ease;
      }

      .btn-success:hover {
        background-color:rgb(222, 92, 142);
      }

      .alert {
          margin-top: 15px;
          padding: 10px;
          border-radius: 5px;
      }
      .alert-danger {
          background-color: #f8d7da;
          color: #721c24;
          border-color: #f5c6cb;
      }
      footer {
          float: center;
      }

    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <h1>เข้าสู่ระบบสำหรับเจ้าหน้าที่</h1>

            <?php
            // แสดงข้อความแจ้งเตือน error จากไฟล์ action หากมี
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // ล้างข้อความหลังจากแสดงแล้ว
            }
            ?>

            <form name="admin_login_form" method="post" action="../Actions/admin_login_action.php">
                <div class="form-group">
                    <label for="staff_id">รหัสเจ้าหน้าที่</label>
                    <input class="form-control" name="staff_id" type="text" id="staff_id" placeholder="กรุณาป้อนรหัสเจ้าหน้าที่" required>
                </div>
                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input class="form-control" name="password" type="password" id="password" placeholder="กรุณาป้อนรหัสผ่าน" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="admin_login" value="เข้าสู่ระบบ" class="btn btn-success">
                </div>
            </form>
            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
