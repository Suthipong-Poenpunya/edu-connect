<?php
// teacher_dashboard.php

// *** 1. PHP: ตรวจสอบและเริ่ม session (ต้องอยู่บนสุด ไม่มี Output ก่อนหน้านี้) ***
// ตรวจสอบว่า session ยังไม่ถูกเริ่ม เพื่อหลีกเลี่ยง error หากมีการ include ไฟล์นี้ซ้ำซ้อน
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// *** 2. PHP: Include ไฟล์กำหนดค่าฐานข้อมูล (ก่อนมี Output) ***
require_once '../config/db.php';
if (!$conn) {
    die("Database connection failed in teacher_dashboard.php: " . mysqli_connect_error());
}
// --

// *** 3. PHP: ตรวจสอบสิทธิ์การเข้าถึง (ก่อนมี Output) ***
$loggedInUserId = $_SESSION['user_id'] ?? null;
$loggedInUserRole = $_SESSION['user_role'] ?? null;
$loggedInUserName = $_SESSION['user_name'] ?? 'อาจารย์';
$loggedInStaffId = $_SESSION['staff_id'] ?? null;

if ($loggedInUserId === null || $loggedInUserRole !== 'Teachers') { // ใช้ 'Teachers' ตามที่พบใน admin_login_action.php
    header("Location: ../admin/admin-login.php");
    exit();
}

// กำหนดชื่อและรหัสอาจารย์จาก Session
$teacher_name = $loggedInUserName;
$teacher_staff_id = $loggedInStaffId;


// *** 4. PHP: จัดการ POST Request (มาจาก Form ในหน้าที่ Include - ต้องอยู่ก่อน HTML) ***
// *** Logic การเพิ่ม/แก้ไข/ลบ ถูกย้ายมาไว้ที่นี่ทั้งหมด ***
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลแล้ว (ควรจะมีจาก require_once ด้านบนแล้ว)
     if (!isset($conn)) {
        require_once '../config/db.php';
    }

    $action = $_POST['action'] ?? ''; // รับค่า action จากฟอร์ม

    switch ($action) {
        case 'add_assignment':
            // รับค่าจาก Form (Form ใน teacher_manage_courses.php จะส่ง course_code มา)
            $course_code = $_POST['course_code'] ?? null;
            $semester = $_POST['semester'] ?? null;
            $academic_year = $_POST['academic_year'] ?? null;

            if ($course_code && $semester && $academic_year) {
                // *** หากตาราง teacher_course_assignments มีทั้ง course_id (INT, NOT NULL) และ course_code (VARCHAR, NOT NULL) ***
                // *** และตาราง courses มี course_code เป็น PK ไม่มี course_id ***
                // *** ต้องคิวรี่หา course_id จาก course_code ที่รับมา ***

                $course_id_to_insert = null;
                $query_get_course_id = "SELECT course_id FROM courses WHERE course_code = ?"; // ค้นหา course_id จาก course_code
                $stmt_get_id = $conn->prepare($query_get_course_id);
                 if ($stmt_get_id) {
                     $stmt_get_id->bind_param("s", $course_code); // ใช้ 's' เพราะ course_code เป็น VARCHAR
                     $stmt_get_id->execute();
                     $result_get_id = $stmt_get_id->get_result();
                     if ($row_get_id = $result_get_id->fetch_assoc()) {
                         $course_id_to_insert = $row_get_id['course_id']; // ได้ course_id มาแล้ว
                     }
                     $stmt_get_id->close();
                 } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งค้นหา ID วิชา: " . $conn->error;
                 }

                if ($course_id_to_insert === null) {
                     $_SESSION['error_message'] = "ไม่พบ ID วิชาสำหรับรหัสวิชาที่เลือก: " . htmlspecialchars($course_code);
                } else {
                    // --- เพิ่มข้อมูลลงในตาราง teacher_course_assignments ---
                    // *** INSERT โดยใส่ทั้ง teacher_id, course_id, semester, academic_year, course_code ***
                    $insert_sql = "INSERT INTO teacher_course_assignments (teacher_id, course_id, semester, academic_year, course_code) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    if ($stmt) {
                        // *** Bind Parameters ***
                        // teacher_id (VARCHAR), course_id (INT), semester (INT), academic_year (INT), course_code (VARCHAR)
                        $stmt->bind_param("siiis", $loggedInStaffId, $course_id_to_insert, $semester, $academic_year, $course_code);

                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = "เพิ่มรายวิชาที่สอนสำเร็จ";
                        } else {
                            if ($conn->errno == 1062) {
                                 $_SESSION['error_message'] = "ไม่สามารถเพิ่มรายวิชาได้: อาจารย์ท่านนี้สอนวิชานี้ในภาคการศึกษาและปีการศึกษานี้อยู่แล้ว";
                            } else {
                                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มรายวิชา: " . $conn->error;
                            }
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งเพิ่มรายวิชา: " . $conn->error;
                    }
                } // end if ($course_id_to_insert !== null)

            } else {
                $_SESSION['error_message'] = "กรุณากรอกข้อมูลรายวิชาให้ครบ";
            }
            // Redirect กลับมาที่หน้านี้หลังจากทำ Action เสร็จ
            header("Location: teacher_dashboard.php?page=manage_courses");
            exit(); // *** Exit หลัง header() ***
            break;

        case 'edit_assignment':
            $assignment_id = $_POST['assignment_id'] ?? null;
            // รับค่าที่แก้ไขจาก Form
            $course_code = $_POST['course_code'] ?? null; // รับ course_code จาก form
            $semester = $_POST['semester'] ?? null;
            $academic_year = $_POST['academic_year'] ?? null;

             if ($assignment_id && $course_code && $semester && $academic_year) {
                 // *** หากตาราง teacher_course_assignments มีทั้ง course_id (INT, NOT NULL) และ course_code (VARCHAR, NOT NULL) ***
                 // *** และตาราง courses มี course_code เป็น PK ไม่มี course_id ***
                 // *** ต้องคิวรี่หา course_id จาก course_code ที่รับมา ***

                 $course_id_to_update = null;
                 $query_get_course_id = "SELECT course_id FROM courses WHERE course_code = ?"; // ค้นหา course_id จาก course_code
                 $stmt_get_id = $conn->prepare($query_get_course_id);
                 if ($stmt_get_id) {
                      $stmt_get_id->bind_param("s", $course_code);
                      $stmt_get_id->execute();
                      $result_get_id = $stmt_get_id->get_result();
                      if ($row_get_id = $result_get_id->fetch_assoc()) {
                          $course_id_to_update = $row_get_id['course_id'];
                      }
                      $stmt_get_id->close();
                 } else {
                     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งค้นหา ID วิชา (แก้ไข): " . $conn->error;
                 }

                 if ($course_id_to_update === null) {
                      $_SESSION['error_message'] = "ไม่พบ ID วิชาสำหรับรหัสวิชาที่เลือก (แก้ไข): " . htmlspecialchars($course_code);
                 } else {
                    // --- แก้ไขข้อมูลในตาราง teacher_course_assignments ---
                     // *** UPDATE โดยใช้ทั้ง course_id และ course_code ***
                     $update_sql = "UPDATE teacher_course_assignments SET course_id = ?, semester = ?, academic_year = ?, course_code = ? WHERE assignment_id = ? AND teacher_id = ?";
                    $stmt = $conn->prepare($update_sql);
                    if ($stmt) {
                         // *** Bind Parameters ***
                         // course_id (INT), semester (INT), academic_year (INT), course_code (VARCHAR), assignment_id (INT), teacher_id (VARCHAR)
                         $stmt->bind_param("iiissi", $course_id_to_update, $semester, $academic_year, $course_code, $assignment_id, $loggedInStaffId);

                        if ($stmt->execute()) {
                            if ($stmt->affected_rows > 0) {
                                 $_SESSION['success_message'] = "แก้ไขข้อมูลรายวิชาที่สอนสำเร็จ";
                            } else {
                                 if($conn->error){
                                     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขรายวิชา: " . $conn->error;
                                 } else {
                                     $_SESSION['warning_message'] = "ไม่พบข้อมูลรายวิชาที่ต้องการแก้ไข หรือไม่มีการเปลี่ยนแปลงข้อมูล";
                                 }
                            }
                        } else {
                             $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขรายวิชา: " . $conn->error;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งแก้ไขรายวิชา: " . $conn->error;
                    }
                } // end if ($course_id_to_update !== null)

            } else {
                $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการแก้ไข";
            }
            header("Location: teacher_dashboard.php?page=manage_courses");
            exit(); // *** Exit หลัง header() ***
            break;

        case 'delete_assignment':
            $assignment_id = $_POST['assignment_id'] ?? null;

            if ($assignment_id) {
                 // --- ลบข้อมูลในตาราง teacher_course_assignments ---
                 $delete_sql = "DELETE FROM teacher_course_assignments WHERE assignment_id = ? AND teacher_id = ?";
                 $stmt = $conn->prepare($delete_sql);
                 if ($stmt) {
                    // สมมติว่า assignment_id เป็น INT, teacher_id เป็น INT/VARCHAR
                     $stmt->bind_param("is", $assignment_id, $loggedInStaffId); // ปรับ 's' ถ้า teacher_id เป็น VARCHAR
                    if ($stmt->execute()) {
                         if ($stmt->affected_rows > 0) {
                             $_SESSION['success_message'] = "ลบรายวิชาที่สอนสำเร็จ";
                        } else {
                             if($conn->error){
                                 $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรายวิชา: " . $conn->error;
                             } else {
                                 $_SESSION['warning_message'] = "ไม่พบรายวิชาที่ต้องการลบ หรือรายวิชานั้นไม่ได้อยู่ในความรับผิดชอบของคุณ";
                             }
                        }
                    } else {
                         $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรายวิชา: " . $conn->error;
                    }
                    $stmt->close();
                 } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งลบรายวิชา: " . $conn->error;
                 }

            } else {
                 $_SESSION['error_message'] = "ไม่พบข้อมูลรายวิชาที่ต้องการลบ";
            }
            header("Location: teacher_dashboard.php?page=manage_courses");
            exit(); // *** Exit หลัง header() ***
            break;

            case 'save_grades':
                // รับข้อมูลผลการเรียนที่ส่งมาจากฟอร์ม (จาก teacher_record_grades.php)
                // $_POST['grades'] จะเป็น array ที่มี enrollment_id เป็น Key และ grade_value เป็น Value
                $submitted_grades = $_POST['grades'] ?? [];
    
                // รับ assignment_id ที่เลือกมา เพื่อช่วยในการ Redirect กลับไปที่หน้ารายชื่อนิสิตวิชาเดิม
                $redirect_assignment_id = $_POST['assignment_id'] ?? ''; // Hidden input ในฟอร์มบันทึกเกรด
    
                $success_count = 0; // นับจำนวนรายการที่บันทึกสำเร็จ
                $error_count = 0;   // นับจำนวนรายการที่เกิดข้อผิดพลาด
    
                if (!empty($submitted_grades)) {
                    // วนลูปประมวลผลแต่ละรายการเกรดที่ส่งมา
                    foreach ($submitted_grades as $enrollment_id => $grade_value) {
                        // ทำความสะอาดและตรวจสอบความถูกต้องของค่าเกรดตามต้องการ
                        $cleaned_grade_value = trim($grade_value);
    
                        // คุณสามารถเพิ่ม Logic การตรวจสอบรูปแบบของเกรดได้ที่นี่ เช่น ใช้ preg_match
                        // if (!preg_match('/^[A-Fa-f][+-]?$|^[PF]$/', $cleaned_grade_value) && $cleaned_grade_value !== '') {
                        //     // จัดการกับเกรดที่ไม่ถูกต้อง
                        //     $_SESSION['error_message'] = "รูปแบบเกรดไม่ถูกต้องสำหรับ enrollment ID: " . htmlspecialchars($enrollment_id);
                        //     $error_count++;
                        //     continue; // ข้ามรายการนี้ไป
                        // }
    
    
                        // ตรวจสอบว่ามีเกรดสำหรับ enrollment_id นี้อยู่ในตาราง grades แล้วหรือยัง
                        $query_check_grade = "SELECT grade_id FROM grades WHERE enrollment_id = ?";
                        $stmt_check = $conn->prepare($query_check_grade);
                        $grade_exists = false; // สมมติว่ายังไม่มีเกรดอยู่
                        if ($stmt_check) {
                            $stmt_check->bind_param("i", $enrollment_id); // enrollment_id เป็น INT
                            $stmt_check->execute();
                            $stmt_check->store_result(); // ต้องใช้ store_result() ก่อน num_rows
                            if ($stmt_check->num_rows > 0) {
                                $grade_exists = true; // พบเกรดสำหรับ enrollment_id นี้แล้ว
                            }
                            $stmt_check->close(); // ปิด statement ตรวจสอบ
                        } else {
                             // หากเตรียมคำสั่งตรวจสอบล้มเหลว ให้บันทึก Error และข้ามรายการนี้ไป
                             error_log("DB Error (Prepare Check Grade): " . $conn->error); // บันทึก error ใน log ของ server
                             $error_count++;
                             continue; // ไปรายการเกรดถัดไป
                        }
    
    
                        if ($grade_exists) {
                            // *** ถ้ามีเกรดอยู่แล้วในตาราง grades ให้ทำการ UPDATE ***
                            // อัปเดตค่าเกรด, อาจารย์ผู้บันทึก และเวลาที่บันทึก
                            $update_grade_sql = "UPDATE grades SET grade_value = ?, recorded_by_teacher_id = ?, recorded_at = CURRENT_TIMESTAMP WHERE enrollment_id = ?";
                            $stmt_update = $conn->prepare($update_grade_sql);
                            if ($stmt_update) {
                                 // Bind parameters: grade_value(s), teacher_id(s), enrollment_id(i) - ปรับ type ของ teacher_id ถ้าเป็น INT (ใน users.staff_id)
                                 $stmt_update->bind_param("ssi", $cleaned_grade_value, $loggedInStaffId, $enrollment_id);
                                 if ($stmt_update->execute()) {
                                     // ตรวจสอบว่ามีแถวที่ถูกอัปเดตจริงหรือไม่ (affected_rows > 0)
                                     // if ($stmt_update->affected_rows > 0) { // อาจจะไม่ได้ affected_rows ถ้าค่าเกรดเหมือนเดิม
                                         $success_count++; // นับรายการที่อัปเดตสำเร็จ
                                     // }
                                 } else {
                                     error_log("DB Error (Execute Update Grade): " . $conn->error); // บันทึก error
                                     $error_count++; // นับรายการที่เกิดข้อผิดพลาด
                                 }
                                 $stmt_update->close(); // ปิด statement อัปเดต
                            } else {
                                 error_log("DB Error (Prepare Update Grade): " . $conn->error); // บันทึก error
                                 $error_count++; // นับรายการที่เกิดข้อผิดพลาด
                            }
    
                        } else {
                            // *** ถ้ายังไม่มีเกรดสำหรับ enrollment_id นี้ ให้ทำการ INSERT ***
                            // เพิ่มรายการใหม่ในตาราง grades
                             // ตรวจสอบว่าค่าเกรดที่กรอกมาไม่ใช่ค่าว่าง ก่อนที่จะทำการ INSERT
                             if ($cleaned_grade_value !== '') {
                                $insert_grade_sql = "INSERT INTO grades (enrollment_id, grade_value, recorded_by_teacher_id) VALUES (?, ?, ?)";
                                $stmt_insert = $conn->prepare($insert_grade_sql);
                                if ($stmt_insert) {
                                     // Bind parameters: enrollment_id(i), grade_value(s), teacher_id(s) - ปรับ type ของ teacher_id ถ้าเป็น INT
                                     $stmt_insert->bind_param("iss", $enrollment_id, $cleaned_grade_value, $loggedInStaffId);
                                     if ($stmt_insert->execute()) {
                                         $success_count++; // นับรายการที่เพิ่มสำเร็จ
                                     } else {
                                         error_log("DB Error (Execute Insert Grade): " . $conn->error); // บันทึก error
                                         $error_count++; // นับรายการที่เกิดข้อผิดพลาด
                                     }
                                     $stmt_insert->close(); // ปิด statement เพิ่ม
                                } else {
                                     error_log("DB Error (Prepare Insert Grade): " . $conn->error); // บันทึก error
                                     $error_count++; // นับรายการที่เกิดข้อผิดพลาด
                                }
                             }
                        }
                    } // จบ foreach loop ประมวลผลเกรดแต่ละรายการ
    
                    // ตั้งค่าข้อความสรุปผลการบันทึก
                    if ($success_count > 0) {
                        $_SESSION['success_message'] = "บันทึก/อัปเดตผลการเรียนสำเร็จ {$success_count} รายการ";
                    }
                    if ($error_count > 0) {
                        $_SESSION['error_message'] = "พบข้อผิดพลาดในการบันทึกผลการเรียน {$error_count} รายการ. โปรดตรวจสอบ Server Log สำหรับรายละเอียด.";
                    }
                    // กรณีที่ส่ง Form มาแต่ไม่ได้กรอกเกรดเลย จะไม่เข้า if (!empty($submitted_grades)) และจะไม่แสดงข้อความใดๆ
    
                } else {
                     // กรณีที่ $_POST['grades'] ว่าง (ไม่มีรายการเกรดส่งมา)
                     // อาจจะแสดง warning หรือไม่ต้องแสดงอะไรเลยก็ได้
                     $_SESSION['warning_message'] = "ไม่พบข้อมูลผลการเรียนที่ส่งมาเพื่อบันทึก"; // แสดง warning
                }
    
                // Redirect กลับไปยังหน้าบันทึกผลการเรียนเดิม โดยส่ง assignment_id กลับไปด้วยเพื่อให้แสดงรายชื่อนิสิตวิชาเดิมทันที
                $redirect_url = "teacher_dashboard.php?page=record_grades";
                if (!empty($redirect_assignment_id)) {
                     // เพิ่ม assignment_id ใน URL เพื่อให้หน้า record_grades โหลดรายชื่อวิชาเดิม
                     $redirect_url .= "&assignment_id=" . urlencode($redirect_assignment_id);
                }
                header("Location: " . $redirect_url);
                exit(); // *** ออกจากสคริปต์หลังจาก Redirect ***
                break; // จบ case 'save_grades'

        default:
            // Action ไม่ถูกต้อง หรือไม่มี action
            break;
    }
    // ไม่ต้องปิด $conn ที่นี่ เพราะจะนำไปใช้ในการดึงข้อมูลแสดงผลต่อในหน้าเดียวกัน
}


// *** 5. PHP: กำหนดหน้าเนื้อหาหลัก (หลังจากจัดการ POST แล้ว) ***
$page = $_GET['page'] ?? 'home';


// *** 6. HTML Output เริ่มต้น (หลังจาก PHP ส่วนบนทั้งหมดทำงานเสร็จ) ***
// *** ไม่มีช่องว่างหรือตัวอักษรใดๆ ก่อนแท็กนี้ ***
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EDU CU E-Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        /* ... สไตล์ CSS เหมือนเดิม ... */
         body { font-family: 'Kanit', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .dashboard-container { display: flex; width: 95%; max-width: 1400px; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 10px; overflow: hidden; min-height: 80vh; }
        .teacher-sidebar { flex: 0 0 220px; background-color: #c84a83; color: #fff; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .sidebar-header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        .sidebar-header h2 { color: #fff; font-size: 1.5em; margin: 0; }
        .teacher-info { font-size: 0.9em; color: rgba(255, 255, 255, 0.8); }
        .teacher-menu ul { list-style: none; padding: 0; margin: 0; }
        .teacher-menu li { margin-bottom: 10px; }
        .teacher-menu a { text-decoration: none; color: #fff; display: block; padding: 10px 15px; border-radius: 5px; transition: background-color 0.3s ease; }
        .teacher-menu a:hover { background-color: rgba(255, 255, 255, 0.2); }
        .logout-section { margin-top: auto; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.3); }
         .logout-section a { text-decoration: none; color: #fff; display: block; padding: 10px 15px; border-radius: 5px; background-color: rgba(255, 255, 255, 0.1); text-align: center; transition: background-color 0.3s ease; }
         .logout-section a:hover { background-color: rgba(255, 255, 255, 0.3); }
        .teacher-main-content { flex-grow: 1; padding: 20px; background-color: #fff; overflow-y: auto; }
        .teacher-main-content h1 { color: #333; margin-bottom: 20px; }
        .welcome-box { background-color: #f0f0f0; padding: 20px; border-radius: 8px; border-left: 5px solid #c84a83; }
        .welcome-box h3 { color: #333; margin-top: 0; }
         .alert { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="teacher-sidebar">
            <div class="sidebar-header">
                <h2>ระบบจัดการข้อมูลสำหรับอาจารย์</h2>
                <div class="teacher-info">
                    ยินดีต้อนรับ, <?php echo htmlspecialchars($teacher_name); ?><br>
                    รหัสอาจารย์: <?php echo htmlspecialchars($teacher_staff_id); ?>
                </div>
            </div>

            <div class="teacher-menu">
                <ul>
                    <li><a href="teacher_dashboard.php?page=home">หน้าหลัก</a></li>
                    <li><a href="teacher_dashboard.php?page=manage_courses">จัดการรายวิชาที่สอน</a></li>
                    <li><a href="teacher_dashboard.php?page=record_grades">บันทึกผลการเรียน</a></li>
                    <li><a href="teacher_dashboard.php?page=create_announcement">สร้างประกาศ</a></li>
                </ul>
            </div>

            <div class="logout-section">
                <a href="../Public/logout.php">ออกจากระบบ</a>
            </div>
        </div>

        <div class="teacher-main-content">

            <?php
            // *** 7. PHP: แสดงข้อความแจ้งเตือน (หลัง Output HTML เริ่ม) ***
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
             if (isset($_SESSION['warning_message'])) {
                echo '<div class="alert alert-warning">' . htmlspecialchars($_SESSION['warning_message']) . '</div>';
                unset($_SESSION['warning_message']);
            }
            ?>

            <?php
            // *** 8. PHP: Include เนื้อหาหลักตามค่า page (ภายใน Main Content Area) ***
            switch ($page) {
                case 'home':
                    // *** เนื้อหาหน้าหลัก อยู่ตรงนี้ หรือ Include ไฟล์แยก เช่น include 'teacher_home.php'; ***
                    ?>
                    <h1>ยินดีต้อนรับ อาจารย์</h1>
                    <div class="welcome-box">
                        <h3>ยินดีต้อนรับสู่ระบบจัดการ EDU CU E-Service สำหรับอาจารย์</h3>
                        <p>คุณสามารถใช้เมนูทางด้านซ้ายเพื่อจัดการรายวิชา บันทึกผลการเรียน และสร้างประกาศ</p>
                        <p>โปรดใช้ความระมัดระวังในการแก้ไขข้อมูล</p>
                    </div>
                    <?php
                    break;

                case 'manage_courses':
                    ?>
                    <h1>จัดการรายวิชาที่สอน</h1>
                    <?php
                    // *** Include ไฟล์แยกสำหรับการจัดการรายวิชาที่นี่ ***
                    // ไฟล์นี้ควรมีเฉพาะ Logic การดึงข้อมูลมาแสดงผลและ HTML/JS
                    include 'teacher_manage_courses.php'; // *** include ที่นี่ ***
                    break;

                case 'record_grades':
                    ?>
                    <h1>บันทึกผลการเรียน</h1>
                    <?php
                    include 'teacher_record_grades.php';
                    break;

                case 'create_announcement':
                    ?>
                    <h1>สร้างประกาศ</h1>
                    <?php
                     include 'teacher_manage_announcements.php';
                    break;

                default:
                    // หาก page ไม่ถูกต้อง ก็ Redirect กลับหน้า home (Header อยู่ด้านบนแล้ว)
                    header("Location: teacher_dashboard.php?page=home");
                    exit(); // *** Exit หลัง header() ***
                    break;
            }
            // --- จบส่วนการแสดงเนื้อหาหลัก ---
            ?>

        </div>
    </div>

    <?php
    // *** 9. PHP: ปิดการเชื่อมต่อฐานข้อมูล (Optional) ***
    // $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
