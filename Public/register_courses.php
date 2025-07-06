<?php
// register_courses.php
session_start(); // เริ่ม Session

// ตรวจสอบว่านิสิตล็อกอินแล้วหรือไม่
if (!isset($_SESSION['student_code'])) {
    header("Location: login.php"); // Redirect ไปหน้า Login ถ้านิสิตยังไม่ได้ล็อกอิน
    exit();
}

// Include ไฟล์ฟังก์ชัน
include('../include/functions.php'); // ให้แน่ใจว่าไฟล์นี้มีฟังก์ชันที่แก้ไขล่าสุด และฟังก์ชันต่างๆ รับ $conn Parameter

// *** สำคัญมาก: Include ไฟล์กำหนดค่าฐานข้อมูลและสร้างการเชื่อมต่อ ***
// ให้แน่ใจว่าไฟล์ ../config/db.php ทำการเชื่อมต่อฐานข้อมูลสำเร็จและกำหนด Object การเชื่อมต่อให้ตัวแปรชื่อ $conn
require_once('../config/db.php'); // <-- ตรวจสอบให้แน่ใจว่าบรรทัดนี้อยู่ตรงนี้และทำงานสำเร็จ

// *** ตรวจสอบว่าการเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่ ***
// หาก $conn มีค่าเป็น NULL หรือเป็น Object ที่ผิดพลาด จะต้องจัดการ Error ก่อน
if (!$conn) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้ใน register_courses.php: " . mysqli_connect_error()); // แสดงข้อความ Error และหยุดการทำงาน
}


// ตรวจสอบและกำหนดภาคการศึกษาและปีการศึกษาปัจจุบัน
// *** ส่งตัวแปร $conn ที่เก็บ Object การเชื่อมต่อฐานข้อมูล ไปยังฟังก์ชัน ***
$current_semester_year = getCurrentSemesterAndYear($conn); // <-- ส่ง $conn ที่นี่
$current_semester = null;
$current_academic_year = null;
$error_message = null; // ตัวแปรสำหรับเก็บข้อความ error ทั่วไป

if ($current_semester_year) {
    $current_semester = $current_semester_year['semester'];
    $current_academic_year = $current_semester_year['academic_year'];
} else {
    // จัดการกรณีที่หาภาคการศึกษาปัจจุบันไม่เจอ
    $error_message = "ไม่สามารถกำหนดภาคการศึกษาและปีการศึกษาปัจจุบันได้. โปรดตรวจสอบการตั้งค่าภาคการศึกษาในฐานข้อมูล.";
    // คุณอาจต้องการหยุดการทำงานต่อหากไม่มีภาคการศึกษาปัจจุบัน
    // die($error_message);
}


// ดึงรายวิชาที่เปิดให้ลงทะเบียน (อาจต้องปรับฟังก์ชันนี้ให้กรองตามภาค/ปีปัจจุบัน)
// *** ส่งตัวแปร $conn ไปยังฟังก์ชัน ***
$available_courses = getAvailableCoursesForRegistration($conn); // <-- ส่ง $conn ที่นี่
if ($available_courses === false) {
    // Error Message จากฟังก์ชันจะถูกส่งกลับมา
    $error_message = "เกิดข้อผิดพลาดในการดึงรายวิชาที่เปิดลงทะเบียน: " . getLastDBError($conn); // <-- ส่ง $conn ไปยัง getLastDBError
    $available_courses = []; // กำหนดเป็น Array ว่างถ้ามี Error
}


// ดึงรายวิชาที่นิสิตลงทะเบียนแล้วสำหรับภาคการศึกษาและปีการศึกษาปัจจุบัน
$registered_courses = []; // กำหนดเป็น Array ว่างก่อน
if ($current_semester !== null && $current_academic_year !== null) {
     // *** ส่ง student_code, semester, year, และ $conn ไปยังฟังก์ชัน ***
     $registered_courses = getRegisteredCourses($_SESSION['student_code'], $current_semester, $current_academic_year, $conn); // <-- ส่ง $conn ที่นี่
      if ($registered_courses === false) {
         // Error Message จากฟังก์ชันจะถูกส่งกลับมา
         $error_message = "เกิดข้อผิดพลาดในการดึงรายวิชาที่ลงทะเบียนแล้ว: " . getLastDBError($conn); // <-- ส่ง $conn ไปยัง getLastDBError
         $registered_courses = []; // กำหนดเป็น Array ว่างถ้ามี Error
      }
} else {
    // error_message ได้ถูกตั้งค่าไว้แล้วที่ด้านบน ถ้าหาภาค/ปี ไม่เจอ
    $registered_courses = []; // กำหนดเป็น Array ว่างถ้าไม่สามารถหาภาค/ปีปัจจุบันได้
}


// ประมวลผลการลงทะเบียน/ยกเลิกการลงทะเบียน (ถ้ามีการ Submit Form)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ใช้ Session ในการเก็บข้อความแจ้งเตือน ( success_message, error_message )
    // และ Redirect กลับมาหน้าเดิมเพื่อป้องกัน Form Resubmission

    if (isset($_POST['register_course'])) {
        $course_code_to_register = $_POST['register_course']; // รับรหัสวิชาจากฟอร์มลงทะเบียน

        if ($current_semester !== null && $current_academic_year !== null) {
            // เรียกใช้ฟังก์ชัน registerCourse โดยส่ง semester, year, และ $conn ไปด้วย
            // *** ส่ง semester, year, และ $conn ไปยังฟังก์ชัน ***
            $registration_result = registerCourse($_SESSION['student_code'], $course_code_to_register, $current_semester, $current_academic_year, $conn); // <-- ส่ง $conn ที่นี่

            if ($registration_result === true) {
                $_SESSION['success_message'] = "ลงทะเบียนเรียนสำเร็จ";
                header("Location: register_courses.php?success=1");
                exit();
            } else {
                // Error message มาจากฟังก์ชัน registerCourse เอง
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลงทะเบียนเรียน: {$registration_result}";
                header("Location: register_courses.php?error=1");
                exit();
            }
        } else {
             $_SESSION['error_message'] = "ไม่สามารถลงทะเบียนเรียนได้: ไม่สามารถกำหนดภาคการศึกษาและปีการศึกษาปัจจุบันได้ หรือยังไม่สามารถลงทะเบียนได้ในภาคการศึกษานี้";
             header("Location: register_courses.php?error=2");   
             exit();
        }

    } elseif (isset($_POST['unregister_course'])) {
        $course_code_to_unregister = $_POST['unregister_course']; // รับรหัสวิชาจากฟอร์มยกเลิก

         if ($current_semester !== null && $current_academic_year !== null) {
             // เรียกใช้ฟังก์ชัน unregisterCourse โดยส่ง semester, year, และ $conn ไปด้วย
             // *** ส่ง semester, year, และ $conn ไปยังฟังก์ชัน ***
             $unregister_result = unregisterCourse($_SESSION['student_code'], $course_code_to_unregister, $current_semester, $current_academic_year, $conn); // <-- ส่ง $conn ที่นี่

             if ($unregister_result === true) {
                $_SESSION['success_message'] = "ยกเลิกการลงทะเบียนสำเร็จ";
             } else {
                // Error message มาจากฟังก์ชัน unregisterCourse เอง
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการยกเลิกการลงทะเบียน: {$unregister_result}";
             }
         } else {
              $_SESSION['error_message'] = "ไม่สามารถยกเลิกการลงทะเบียนได้: ไม่สามารถกำหนดภาคการศึกษาและปีการศึกษาปัจจุบันได้";
         }
         // Redirect กลับมาหน้าเดิมหลังประมวลผล POST
         header("Location: register_courses.php?success=1");
         exit();
    }
}

// ดึงข้อความแจ้งเตือนจาก Session เพื่อแสดงผล (หลังประมวลผล POST และก่อน Output HTML)
$display_message = '';
if (isset($_SESSION['success_message'])) {
    $display_message = "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $display_message = "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
    unset($_SESSION['error_message']);
}


?>
<!DOCTYPE html>
<html lang="th">
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนเรียน - ระบบ e-Service มหาวิทยาลัย</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        title {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8; /* สีพื้นหลังอ่อนๆ */
        }
        button {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8; /* สีพื้นหลังอ่อนๆ */
        }
        .search-bar {
            padding: 10px;
            border: 1px solid #DE5C8E;
            border-radius: 25px; /* ทำให้กลมมน */
            width: 500px;
            font-size: 1em;
            font-family: 'Kanit';/* ใช้ Font Kanit */
            transition: box-shadow 0.3s ease;
            outline: none;
        }
        .search-bar:focus {
            box-shadow: 0 0 5px rgba(222, 92, 142, 0.5); /* เพิ่มเงาเมื่อ Focus */
        }
        .search-bar2 {
            font-family: 'Kanit';/* ใช้ Font Kanit */
            border-radius: 5px;
            size: 100%;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff; /* สีขาว */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #DE5C8E; /* สีชมพู */
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .available-courses h2, .registered-courses h3 {
            color: #DE5C8E; /* สีชมพู */
            margin-bottom: 15px;
            font-size: 1.8em;
            border-bottom: 2px solid #DE5C8E;
            padding-bottom: 10px;
        }
        .course-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .course-item {
            background-color: #ffffff; /* สีขาว */
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 45%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .course-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .course-code {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        .course-info {
            font-size: 1.1em;
            color: #555;
        }
        .register-button, .unregister-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .register-button {
            background-color: #DE5C8E; /* สีชมพู */
            color: #ffffff; /* สีขาว */
        }
        .register-button:hover {
            background-color: #C53E77; /* สีชมพูเข้มขึ้น */
        }
        .unregister-button {
            background-color: #ffffff; /* สีขาว */
            color: #DE5C8E; /* สีชมพู */
            border: 1px solid #DE5C8E;
        }
        .unregister-button:hover {
            background-color: #f0f0f0; /* สีเทาอ่อน */
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
            font-size: 1.1em;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .registered-courses ul {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .registered-courses ul li {
            background-color: #ffffff; /* สีขาว */
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            font-size: 1.1em;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .registered-courses ul li:nth-child(odd) {
            background-color: #f8f8f8; /* สีเทาอ่อน */
        }
        a {
            color: #DE5C8E;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 1.1em;
            margin-top: 15px;
            display: inline-block;
        }
        a:hover {
            color: #C53E77;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            .course-item {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
            }
            .course-item button {
                margin-top: 10px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ลงทะเบียนเรียน</h1>
        <div class="search-bar">
            <label for="search_keyword">ค้นหารหัสวิชา:</label>
            <input type="text" id="search_keyword" name="search_keyword" class="search-bar2" placeholder="กรอกรหัสวิชาที่ต้องการค้นหา">
            <button class="search-bar2" >ค้นหา</button>
            </div>

        <h2>รายวิชาที่เปิดให้ลงทะเบียน</h2>
        <div class="course-list">
        <?php if (!empty($available_courses)): ?>
            <?php foreach ($available_courses as $course): ?>
                <div class="course-item">
                    <div class="course-details">
                        <strong><?php echo $course['course_code']; ?></strong> - <?php echo $course['course_name']; ?> (<?php echo $course['credits']; ?> หน่วยกิต)
                        <p>รายละเอียด: <?php echo $course['description']; ?></p>
                        <p>วัน/เวลา: <?php echo $course['DAY']; ?> <?php echo $course['TIME']; ?></p>
                        <p>จำนวนที่นั่ง: <?php echo $course['available_seats']; ?> / <?php echo $course['total_seats']; ?></p>
                    </div>
                    <div class="course-actions">
                        <?php if (!in_array($course['course_code'], array_column($registered_courses, 'course_code'))): ?>
                            <form method="post">
                                <input type="hidden" name="register_course" value="<?php echo $course['course_code']; ?>">
                                <button type="submit" class="register-button">ลงทะเบียน</button>
                            </form>
                        <?php else: ?>
                            <span style="color: green;">ลงทะเบียนแล้ว</span>
                            <form method="post">
                                <input type="hidden" name="unregister_course" value="<?php echo $course['course_code']; ?>">
                                <button type="submit" class="unregister-button">ยกเลิก</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่มีรายวิชาที่เปิดให้ลงทะเบียนในขณะนี้</p>
        <?php endif; ?>
        </div>

        <?php if (isset($registration_message)): ?>
            <div class="alert"><?php echo $registration_message; ?></div>
        <?php endif; ?>

        <?php if (isset($unregistration_message)): ?>
            <div class="alert"><?php echo $unregistration_message; ?></div>
        <?php endif; ?>

        <div class="registered-courses">
            <h3>วิชาที่ลงทะเบียนแล้ว</h3>
            <?php if (!empty($registered_courses)): ?>
                <ul>
                    <?php foreach ($registered_courses as $registered_course): ?>
                        <li><?php echo $registered_course['course_code']; ?> - <?php echo $registered_course['course_name']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>ยังไม่มีวิชาที่ลงทะเบียน</p>
            <?php endif; ?>
        </div>

        <p><a href="dashboard.php">กลับสู่แดชบอร์ด</a></p>
    </div>
</body>
</html>