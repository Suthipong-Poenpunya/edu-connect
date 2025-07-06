<?php
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        max-width: 400px;
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

      .register-link {
        margin-top: 15px;
        text-align: center;
      }

      .register-link a {
        color:rgb(222, 92, 142);
        text-decoration: none;
        transition: color 0.3s ease;
      }

      .register-link a:hover {
        color:rgb(222, 92, 142);
        text-decoration: underline;
      }
      
    </style>
</head>
<body>
    <div class="container">
    <div class="logo-container">
    <h1>ยินดีต้อนรับ</h1>
        <form name="form 1" method="post" action="..\Actions\login_action.php">
            <div class="form-group">
                <label for="student_code">รหัสนิสิต</label>
                <input class="form-control" name="student_code" type="text" id="student_code" placeholder="กรุณาป้อนรหัสนิสิต">
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input class="form-control" name="password" type="password" id="password" placeholder="กรุณาป้อนรหัสผ่าน">
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="เข้าสู่ระบบ" class="btn btn-success">
            </div>
        </form>
        </form>
        <div class="register-link">
               <a href="register_student.php">ลงทะเบียนสำหรับนิสิตใหม่</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
