<?php
// Start the session to use session messages
session_start();

// Include the database configuration file
// Make sure the path to db.php is correct relative to this file's location
include '../config/db.php';

// Note: This page is intended to be accessed internally or via a secure link.
// In a production environment, you should implement proper access control
// (e.g., check if the logged-in user has admin privileges)
// before allowing access to this page.
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลเจ้าหน้าที่/อาจารย์ - EDU CU E-Service</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        max-width: 500px; /* Slightly wider container for more fields */
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

      .btn-primary { /* Using btn-primary for add action */
        background-color: #007bff; /* Bootstrap primary blue */
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1em;
        width: 100%;
        transition: background-color 0.3s ease;
      }

      .btn-primary:hover {
        background-color: #0056b3; /* Darker blue on hover */
      }

      .alert {
          margin-top: 15px;
          padding: 10px;
          border-radius: 5px;
      }
      .alert-success {
          background-color: #d4edda;
          color: #155724;
          border-color: #c3e6cb;
      }
      .alert-danger {
          background-color: #f8d7da;
          color: #721c24;
          border-color: #f5c6cb;
      }

    </style>
</head>
<body>
    <div class="container">
        <h1>เพิ่มข้อมูลเจ้าหน้าที่/อาจารย์ใหม่</h1> <?php
        // Display success or error messages from action page
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Clear the message
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message
        }
        ?>

        <form name="add_user_form" method="post" action="../Actions/add_user_action.php"> <div class="form-group">
                <label for="name">ชื่อ-นามสกุล</label>
                <input class="form-control" name="name" type="text" id="name" placeholder="กรุณาป้อนชื่อ-นามสกุล" required>
            </div>
            <div class="form-group">
                <label for="email">อีเมล</label>
                <input class="form-control" name="email" type="email" id="email" placeholder="กรุณาป้อนอีเมล" required>
            </div>
            <div class="form-group">
                <label for="staff_id">รหัสเจ้าหน้าที่/อาจารย์</label> <input class="form-control" name="staff_id" type="text" id="staff_id" placeholder="กรุณาป้อนรหัสเจ้าหน้าที่หรืออาจารย์" required> </div>
            <div class="form-group">
            <div class="form-group">
                <label for="referral_code">รหัสอ้างอิง (ได้รับจากเจ้าหน้าที่)</label> <input class="form-control" name="referral_code" type="text" id="referral_code" placeholder="กรุณาป้อนรหัสเจ้าหน้าที่หรืออาจารย์" required> </div>
            <div class="form-group"></div>
                <label for="password">รหัสผ่าน</label>
                <input class="form-control" name="password" type="password" id="password" placeholder="กรุณาป้อนรหัสผ่าน" required>
            </div>

            <div class="form-group">
                <label for="role">บทบาท</label> <select class="form-control" name="role" id="role" required>
                    <option value="">เลือกบทบาท</option>
                    <option value="staff">เจ้าหน้าที่ ผู้บริหาร</option>
                    <option value="teacher">อาจารย์</option>
                    </select>
            </div>


            <div class="form-group">
                <input type="submit" name="add_user" value="เพิ่มข้อมูล" class="btn btn-primary"> </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
