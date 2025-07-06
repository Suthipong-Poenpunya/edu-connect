<?php
require_once("../config/db.php"); // ตรวจสอบให้แน่ใจว่า path ไปยัง db.php ถูกต้อง

// เริ่ม session หากยังไม่ได้เริ่ม (จำเป็นสำหรับการใช้งาน $_SESSION)
$conn->set_charset("utf8mb4");


// เริ่ม session หากยังไม่ได้เริ่ม (จำเป็นสำหรับการใช้งาน $_SESSION)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$student_code = ''; // กำหนดค่าเริ่มต้น
$error_message = '';
$success_message = '';
$found_id = null; // ตัวแปรสำหรับเก็บค่า id ที่ได้จาก URL

// --- ส่วนสำคัญ: ดึง id จาก URL และใช้ค้นหา student_code ---
// ดึงค่า id จาก URL (ใช้ $_GET)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // ตรวจสอบว่าเป็นตัวเลขหรือไม่เพื่อความปลอดภัย
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $found_id = (int)$_GET['id']; // แปลงเป็น integer

        // ใช้ id ที่ได้มา ค้นหา student_code ในตาราง students
        $sql_get_student_code = "SELECT student_code FROM students WHERE id = ? LIMIT 1";
        $stmt_get_code = null;

        try {
            $stmt_get_code = $conn->prepare($sql_get_student_code);
            if ($stmt_get_code === false) {
                throw new Exception("เตรียมคำสั่ง SQL สำหรับดึง student_code ไม่สำเร็จ: " . $conn->error);
            }
            $stmt_get_code->bind_param("i", $found_id); // ใช้ "i" สำหรับ integer id
            $stmt_get_code->execute();
            $result_get_code = $stmt_get_code->get_result();

            if ($result_get_code->num_rows > 0) {
                // พบ student_code แล้ว นำมาเก็บไว้ในตัวแปร $student_code
                $row = $result_get_code->fetch_assoc();
                $student_code = $row['student_code'];
                 // เก็บ $student_code ไว้ใน session ชั่วคราวเพื่อใช้ยืนยันใน POST request
                 $_SESSION['current_student_code'] = $student_code;
                 // เก็บ $found_id ไว้ใน session ชั่วคราวเพื่อใช้ส่งต่อไปหน้า success
                 $_SESSION['current_found_id'] = $found_id; // เก็บ id ไว้ใน session ด้วย

            } else {
                // ไม่พบ student_code สำหรับ id ที่ระบุในฐานข้อมูล
                $error_message = "ไม่พบข้อมูลรหัสนิสิตสำหรับ ID ที่ระบุ กรุณาลองใหม่อีกครั้ง";
                // หากต้องการ redirect กลับไปหน้าค้นหา:
                // header("Location: search_student.php"); // เปลี่ยน search_student.php เป็นชื่อไฟล์หน้าที่ค้นหา
                // exit();
            }

        } catch (Exception $e) {
            $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูลรหัสนิสิต: " . $e->getMessage();
        } finally {
            // ปิด statement ถ้ามีการเตรียมไว้
            if ($stmt_get_code !== null) {
                $stmt_get_code->close();
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


// --- ส่วนของการจัดการ POST request (เมื่อฟอร์มถูก Submit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงค่า student_code จาก hidden input ในฟอร์ม
    $posted_student_code = $_POST['student_code'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? ''; // ดึงค่าจากช่องยืนยันรหัสผ่าน

    // ดึง student_code ที่ถูกต้องจาก session เพื่อใช้ในการยืนยัน
    $correct_student_code = $_SESSION['current_student_code'] ?? '';
    // ดึง id ที่ถูกต้องจาก session เพื่อใช้ในการ redirect
    $redirect_id = $_SESSION['current_found_id'] ?? null;


    // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วน
    if (empty($posted_student_code) || empty($new_password) || empty($confirm_password)) {
        $error_message = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif ($new_password !== $confirm_password) { // ตรวจสอบว่ารหัสผ่านใหม่และยืนยันรหัสผ่านตรงกันหรือไม่
        $error_message = "รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน";
    } elseif ($posted_student_code !== $correct_student_code || empty($correct_student_code) || empty($redirect_id)) { // ตรวจสอบความถูกต้องของ student_code และ id
         // ตรวจสอบว่า student_code ที่ส่งมาจากฟอร์ม ตรงกับค่าที่ควรจะเป็นจาก Session หรือไม่
         // และต้องแน่ใจว่ามีค่า student_code และ id ที่ถูกต้องใน Session
         $error_message = "ข้อมูลรหัสนิสิตไม่ถูกต้อง ข้อมูล ID หายไป หรือเซสชันหมดอายุ กรุณาลองใหม่อีกครั้ง";
         // หากเกิด error ที่นี่ อาจจะต้อง clear session และ redirect กลับไปหน้าค้นหา
         unset($_SESSION['current_student_code']);
         unset($_SESSION['current_found_id']);
         // header("Location: search_student.php"); exit();

    } else {
         // ข้อมูลครบถ้วนและรหัสผ่านตรงกัน ทำการอัปเดตรหัสผ่าน
        // ในตัวอย่างนี้จะ hash รหัสผ่านก่อนบันทึกลงฐานข้อมูล
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // ตรวจสอบว่า $conn object มีค่าหรือไม่ และเชื่อมต่อสำเร็จหรือไม่
        if (!isset($conn) || $conn->connect_error) {
             $error_message = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . ($conn->connect_error ?? "Unknown error");
        } else {
            $update_sql = "UPDATE students SET password = ? WHERE student_code = ?";
            $stmt = null; // กำหนดค่าเริ่มต้นเป็น null

            try {
                // เตรียมคำสั่ง SQL
                $stmt = $conn->prepare($update_sql);
                if ($stmt === false) {
                     throw new Exception("เตรียมคำสั่ง UPDATE SQL ไม่สำเร็จ: " . $conn->error);
                }
                $stmt->bind_param("ss", $hashed_password, $posted_student_code);

                // พยายาม実行 (execute) คำสั่ง
                $stmt->execute();

                // ตรวจสอบว่ามีแถวที่ถูกอัปเดตหรือไม่
                if ($stmt->affected_rows > 0) {
                     // การ UPDATE สำเร็จ

                    // ไม่ต้องลบ $_SESSION['current_found_id'] ที่นี่
                    // เพราะต้องการใช้ค่านี้ในการ Redirect ไปยัง success.php

                    // Redirect ไปยังหน้า success.php พร้อมส่ง id ไปด้วย
                    header("Location: ../Public/success.php?id=" . $redirect_id); // ใช้ $redirect_id ที่ดึงมาจาก session
                    exit();

                } else {
                    // กรณีที่ execute สำเร็จ แต่ไม่มีแถวไหนถูกอัปเดต (เช่น student_code ไม่ถูกต้องใน DB)
                    $error_message = "ไม่พบรหัสนิสิตที่ระบุในระบบ หรือรหัสผ่านไม่ได้ถูกเปลี่ยนแปลง";
                }

            } catch (mysqli_sql_exception $e) {
                // ดักจับ Exception ที่เกิดจากฐานข้อมูล
                 $error_message = "เกิดข้อผิดพลาดในการตั้งรหัสผ่าน (DB): " . $e->getMessage();
            } catch (Exception $e) {
                 // ดักจับ Exception อื่นๆ
                 $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
            } finally {
                // ปิด statement ถ้ามีการเตรียมไว้
                 if ($stmt !== null) {
                    $stmt->close();
                }
            }
        }
    }
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
    <title>ตั้งรหัสผ่านใหม่</title>
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
            text-align: center; /* จัดข้อความตรงกลาง container */
        }
        h1 {
            text-align: center;
            color: #c84a83;
            margin-bottom: 20px;
        }
        h2 {
             text-align: center;
             color: #555;
             margin-bottom: 20px;
             font-size: 1.2em;
        }
        .student-code-display {
            background-color: #e0e0e0; /* สีพื้นหลังสำหรับแสดงรหัสนิสิต */
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 1.1em;
            color: #333;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left; /* จัดให้ label และ input ชิดซ้ายภายใน form-group */
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* เพื่อให้ padding ไม่เพิ่มขนาดของ input */
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #c84a83;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #a33b6d; /* สีเข้มขึ้นเมื่อโฮเวอร์ */
        }
        /* เพิ่มเติม: จัดการข้อความแจ้งเตือน (ถ้ามี) */
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        p{
            color: red;
            margin: 0px;
        }
         .success-message {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
        button[type="submit"] {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>ตั้งรหัสผ่านใหม่</h1>
    <h2>พบข้อมูลนิสิตในระบบทะเบียนแล้ว</h2>

    <?php
    // แสดงรหัสนิสิตเฉพาะเมื่อมีค่าและไม่มีข้อผิดพลาด
    if (!empty($student_code) && empty($error_message)):
    ?>
        <div class="student-code-display">
            รหัสนิสิตของคุณคือ: <?php echo htmlspecialchars($student_code); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php
    // แสดงฟอร์มเมื่อมีรหัสนิสิตและไม่มีข้อความ error หรือ success
    // ตรวจสอบ $student_code อีกครั้งเพื่อให้แน่ใจว่ามีค่าก่อนแสดงฟอร์ม
    if (!empty($student_code) && empty($error_message) && empty($success_message)):
    ?>
        <form method="post" action="">
            <input type="hidden" name="student_code" value="<?php echo htmlspecialchars($student_code); ?>">

            <div class="form-group">
                <label for="new_password">กรุณาตั้งรหัสผ่าน CUNET</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">ยืนยันรหัสผ่าน CUNET</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="text-center">
                <p>กรุณาจดจำรหัสผ่านที่ตั้งไว้</p>
                <p>ระบบจะไม่มีการแสดงผลรหัสผ่านหรือส่งให้ในภายหลัง</p>
            </div>
            <button type="submit">ตั้งรหัสผ่าน</button>
        </form>
    <?php endif; ?>

</div>

</body>
</html>