<?php
// ตรวจสอบว่ามีการล็อกอินแล้วหรือไม่
session_start();
if (!isset($_SESSION['student_code'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// นำเข้าไฟล์ที่ใช้สำหรับฟังก์ชันที่เกี่ยวข้องกับการดึงข้อมูลจากฐานข้อมูล
include('../include/functions.php');

// ดึงข้อมูลนักเรียนจากฐานข้อมูล
// ปรับให้ดึงข้อมูลจากตาราง student และ student_info
$student_data = getStudentData($_SESSION['student_code']);

// ตรวจสอบว่ามีข้อมูลนักเรียนหรือไม่
if (!$student_data) {
    echo "ไม่พบข้อมูลนักเรียน";
    exit();
}

// กำหนดธีมสี
$primary_color = "rgb(222, 92, 142)"; // สีชมพู
$secondary_color = "#FFFFFF"; // สีขาว
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลนิสิต</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: <?php echo $secondary_color; ?>;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: <?php echo $primary_color; ?>;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 20px auto;
            border: 5px solid <?php echo $primary_color; ?>;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }


        .info-group {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .info-group h2 {
            color: <?php echo $primary_color; ?>;
            margin-bottom: 10px;
            font-size: 1.5em;
            border-bottom: 2px solid <?php echo $primary_color; ?>;
            padding-bottom: 5px;
        }

        .info-group p {
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .edit-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: <?php echo $primary_color; ?>;
            color: <?php echo $secondary_color; ?>;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1em;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .edit-button:hover {
            background-color: #c84a83;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ข้อมูลนิสิต</h1>

        <div class="profile-picture">
            <?php
            // กำหนด path ไปยังรูปภาพ
            $image_path = "../images/student_image/" . $student_data['student_code'] . ".png"; // เปลี่ยน .jpg เป็น .png หรือนามสกุลไฟล์ที่ถูกต้อง

            // ตรวจสอบว่าไฟล์ภาพมีอยู่จริงหรือไม่
            if (file_exists($image_path)) {
                echo '<img src="' . $image_path . '" alt="รูปภาพนิสิต">';
            } else {
                // แสดงภาพ default ถ้าไม่มีภาพของนิสิต
                echo '<img src="../images/default.jpg" alt="รูปภาพนิสิต">'; // ต้องมีไฟล์ default.jpg ใน folder images
            }
            ?>
        </div>

        <div class="info-group">
            <h2>ข้อมูลทั่วไป</h2>
            <p><strong>รหัสนิสิต:</strong> <?php echo $student_data['student_code']; ?></p>
            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo $student_data['first_name'] . ' ' . $student_data['last_name']; ?></p>
            <p><strong>คณะ:</strong> <?php echo $student_data['faculty_name']; ?></p>
            <p><strong>สาขาวิชา:</strong> <?php echo $student_data['major_name']; ?></p>
            <p><strong>ชั้นปี:</strong> <?php echo $student_data['year']; ?></p>
        </div>

        <div class="info-group">
            <h2>ข้อมูลติดต่อ</h2>
            <p><strong>อีเมล:</strong> <?php echo $student_data['email']; ?></p>
            <p><strong>เบอร์โทรศัพท์:</strong> <?php echo $student_data['phone_number']; ?></p>
        </div>

        <div class="info-group">
            <h2>ข้อมูลเพิ่มเติม</h2>
             <p><strong>วันเกิด:</strong> <?php echo $student_data['date_of_birth']; ?></p>
             <p><strong>ที่อยู่:</strong> <?php echo $student_data['address']; ?></p>
        </div>

        <a href="edit_profile.php" class="edit-button">แก้ไขข้อมูลส่วนตัว</a>
        <p><a href="dashboard.php">กลับสู่แดชบอร์ด</a></p>
    </div>
</body>
</html>
