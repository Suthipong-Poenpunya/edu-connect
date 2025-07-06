<?php
include('../config/db.php'); // รวมไฟล์เชื่อมต่อฐานข้อมูล
// เริ่ม session หากยังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตั้งค่า character set เป็น utf8mb4 เพื่อรองรับภาษาไทยและอิโมจิ
$conn->set_charset("utf8mb4");


$student_code = null;
$hashed_password_from_db = null; // ตัวแปรสำหรับเก็บรหัสผ่านที่ Hash แล้วจาก DB
$error_message = '';
$found_id = null; // ตัวแปรสำหรับเก็บค่า id ที่ได้จาก URL
$new_password = null; // ตัวแปรสำหรับเก็บรหัสผ่านใหม่ที่ผู้ใช้กรอก

// --- ส่วนสำคัญ: ดึง id จาก URL และใช้ค้นหา student_code และ password ---
// ดึงค่า id จาก URL (ใช้ $_GET)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // ตรวจสอบว่าเป็นตัวเลขหรือไม่เพื่อความปลอดภัย
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $found_id = (int)$_GET['id']; // แปลงเป็น integer

        // ใช้ id ที่ได้มา ค้นหา student_code และ password ในตาราง students
        $sql_get_student_info = "SELECT student_code, password FROM students WHERE id = ? LIMIT 1";
        $stmt_get_info = null;

        try {
            $stmt_get_info = $conn->prepare($sql_get_student_info);
            if ($stmt_get_info === false) {
                throw new Exception("เตรียมคำสั่ง SQL สำหรับดึงข้อมูลนิสิตไม่สำเร็จ: " . $conn->error);
            }
            $stmt_get_info->bind_param("i", $found_id); // ใช้ "i" สำหรับ integer id
            $stmt_get_info->execute();
            $result_get_info = $stmt_get_info->get_result();

            if ($result_get_info->num_rows > 0) {
                // พบข้อมูลนิสิตแล้ว
                $row = $result_get_info->fetch_assoc();
                $student_code = $row['student_code'];
                $hashed_password_from_db = $row['password']; // ดึงรหัสผ่านที่ Hash แล้ว
                $new_password = password_verify($new_password,$hashed_password_from_db);// ตรวจสอบรหัสผ่านที่ Hash แล้ว

                // เก็บ student_code ไว้ใน session ชั่วคราวเผื่อต้องการใช้ในหน้าอื่น (ถ้าจำเป็น)
                // $_SESSION['current_student_code_success'] = $student_code;

            } else {
                // ไม่พบข้อมูลนิสิตสำหรับ id ที่ระบุในฐานข้อมูล
                $error_message = "ไม่พบข้อมูลนิสิตสำหรับ ID ที่ระบุ กรุณาลองใหม่อีกครั้ง";
                // หากต้องการ redirect กลับไปหน้าค้นหา:
                // header("Location: search_student.php"); // เปลี่ยน search_student.php เป็นชื่อไฟล์หน้าที่ค้นหา
                // exit();
            }

        } catch (Exception $e) {
            $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูลนิสิต: " . $e->getMessage();
        } finally {
            // ปิด statement ถ้ามีการเตรียมไว้
            if ($stmt_get_info !== null) {
                $stmt_get_info->close();
            }
        }
    } else {
        // ค่า id ที่ส่งมาไม่ใช่ตัวเลข
        $error_message = "ข้อมูล ID นิสิตไม่ถูกต้อง";
         // หากต้องการ redirect กลับไปหน้าค้นหา:
        // header("Location: search_student.php"); // เปลี่ยน search_student.php เป็นชื่อไฟล์หน้าที่ค้นหา
        // exit();
    }

} else {
    // กรณีที่ไม่พบ id ใน URL หรือค่าว่าง
    $error_message = "ไม่พบข้อมูล ID นิสิต กรุณาลองใหม่อีกครั้ง หรือข้อมูลไม่ถูกต้อง";
    // หากต้องการ redirect กลับไปหน้าค้นหา:
    // header("Location: search_student.php"); // เปลี่ยน search_student.php เป็นชื่อไฟล์หน้าที่ค้นหา
    // exit();
}
// --- สิ้นสุดส่วนสำคัญ ---


// ส่วนจัดการเมื่อคลิกปุ่มไปหน้าเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_to_login'])) {
    // ลบ session variables ที่เกี่ยวข้องกับการตั้งรหัสผ่าน
    unset($_SESSION['success_student_code']); // ลบ session ที่เคยใช้ส่ง student_code มา
    unset($_SESSION['current_student_code']); // ลบ session ที่เคยใช้ยืนยัน student_code
    unset($_SESSION['found_id']); // ลบ session ที่เคยใช้ส่ง id มา
    // unset($_SESSION['current_student_code_success']); // ลบ session ที่เก็บ student_code ในหน้านี้ (ถ้าใช้)

    // Redirect ไปยังหน้าเข้าสู่ระบบ
    header("Location: login.php"); // เปลี่ยน login.php เป็นชื่อไฟล์หน้าเข้าสู่ระบบของคุณ
    exit();
}

// ปิดการเชื่อมต่อฐานข้อมูลเมื่อจบการทำงานของสคริปต์
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งรหัสผ่านสำเร็จ</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            text-align: center;
            color: #28a745; /* สีเขียวสำหรับสำเร็จ */
            margin-bottom: 20px;
        }
        .info-display {
            background-color: #e9ecef; /* สีพื้นหลังสำหรับแสดงข้อมูล */
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: left;
            word-break: break-all; /* ป้องกันข้อความยาวเกิน */
        }
        .info-display strong {
            color: #333;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .text-center {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #c84a83; /* สีน้ำเงินสำหรับปุ่ม */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            font-family: 'Kanit', sans-serif; /* ใช้ฟอนต์เดียวกัน */
        }
        button:hover {
            background-color: #c84a83; /* สีน้ำเงินเข้มขึ้นเมื่อโฮเวอร์ */
        }
    </style>
</head>
<body>

<div class="container">
    <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php else: ?>
        <h1>ตั้งรหัสผ่านสำเร็จ!</h1>
        <p>รหัสผ่านของคุณได้รับการตั้งค่าเรียบร้อยแล้ว</p>

        <?php if ($student_code): ?>
            <div class="info-display">
                <strong>รหัสนิสิต:</strong> <?php echo htmlspecialchars($student_code); ?>
            </div>
        <?php endif; ?>

        <?php if ($hashed_password_from_db): ?>
             <div class="text-center">
                <strong>กรุณาใช้รหัสผ่านที่ตั้งเมื่อสักครู่ในการเข้าสู่ระบบ</strong>
            </div>
             <?php endif; ?>

        <form method="post" action="">
            <button type="submit" name="go_to_login">ไปหน้าเข้าสู่ระบบ</button>
        </form>

    <?php endif; ?>

</div>

</body>
</html>
