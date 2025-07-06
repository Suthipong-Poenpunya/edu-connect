<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนนิสิตใหม่</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        a {
            text-decoration: none;
            color: #c84a83; /* สีชมพู */
            margin-left: 10px;
        }
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
        }
        h1 {
            text-align: center;
            color: #c84a83;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* เพื่อให้ padding ไม่เพิ่มขนาดของ input */
        }
        button[type="submit"] {
            /*font-family: 'Kanit', sans-serif; /* แก้ไข: ลบ <font-family...> syntax ที่ผิดออก */
            width: 100%;
            padding: 12px;
            background-color: #c84a83;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
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
    </style>
</head>
<body>

<?php
// ตรวจสอบว่าไฟล์ db.php มีอยู่ก่อน include
if (!file_exists('../config/db.php')) {
    die("Error: Database configuration file not found!");
}
include '../config/db.php'; // ไฟล์นี้ต้องสร้างตัวแปร $conn สำหรับการเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า $conn ถูกสร้างและเชื่อมต่อสำเร็จ
if (!$conn) {
     die("Database connection failed: " . mysqli_connect_error());
}


// รับค่าจากฟอร์ม
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$thaiid = $_POST['thaiid'] ?? '';
$error_message = '';
$stmt = null; // กำหนดค่าเริ่มต้นสำหรับ students insert
$check_stmt = null; // กำหนดค่าเริ่มต้นสำหรับ student_code check
$update_stmt = null; // กำหนดค่าเริ่มต้นสำหรับ students update
$insert_info_stmt = null; // กำหนดค่าเริ่มต้นสำหรับ student_info insert
$insert_docs_stmt = null; // กำหนดค่าเริ่มต้นสำหรับ students_documents insert
$delete_stmt = null; // กำหนดค่าเริ่มต้นสำหรับ delete


// ตรวจสอบว่ากรอกครบและทำการประมวลผลเมื่อเป็น POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($first_name && $last_name && $thaiid) {
        // --- Step 1: เพิ่มข้อมูลนิสิตเบื้องต้นในตาราง students ---
        // student_code ตอนนี้อนุญาตให้เป็น NULL ได้แล้ว ตามที่เราแก้ไขโครงสร้างตาราง
        $sql = "INSERT INTO students (first_name, last_name, thaiid) VALUES (?, ?, ?)";

        try {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Prepare INSERT students SQL failed: {$conn->error}");
            }
            $stmt->bind_param("sss", $first_name, $last_name, $thaiid);
            $stmt->execute();

            // ดึง ID ที่ถูกสร้างขึ้นจากการ INSERT ในตาราง students
            $last_id = $conn->insert_id;

            // สร้าง student_code จาก last_id
            $student_code = "674" . str_pad($last_id, 5, '0', STR_PAD_LEFT) . "27";

            // --- Step 2: ตรวจสอบความซ้ำซ้อนของ student_code ที่สร้างขึ้น (ในตาราง students) ---
            $check_sql = "SELECT COUNT(*) FROM students WHERE student_code = ?";
            $check_stmt = $conn->prepare($check_sql);

            if ($check_stmt === false) {
                 throw new Exception("Prepare SELECT student_code check SQL failed: {$conn->error}");
            }

            $check_stmt->bind_param("s", $student_code);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close(); // ปิด statement การตรวจสอบ

            if ($count > 0) {
                // ถ้า student_code ที่สร้างขึ้นบังเอิญซ้ำ (ไม่ควรเกิดบ่อยนัก)
                // ลบแถวที่เพิ่ง INSERT ไปก่อนหน้านี้ออก เพื่อไม่ให้มีข้อมูลค้างในตาราง students
                $delete_sql = "DELETE FROM students WHERE id = ?";
                if ($delete_stmt = $conn->prepare($delete_sql)) {
                     $delete_stmt->bind_param("i", $last_id);
                     $delete_stmt->execute();
                     // ไม่ต้อง close $delete_stmt ที่นี่ เพราะจะไปปิดใน finally
                } else {
                     error_log("Failed to prepare DELETE students with id {$last_id} after duplicate student_code check. Error: {$conn->error}");
                }

                $error_message = "Cannot create student code because it already exists. Please try again or contact support.";
                // ไม่ต้อง redirect หรือไปหน้าต่อไป

            } else {
                // --- Step 3: ถ้า student_code ไม่ซ้ำ ทำการ UPDATE แถวในตาราง students เพื่อใส่ student_code ---
                $update_sql = "UPDATE students SET student_code = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                if ($update_stmt === false) {
                    // ถ้า prepare UPDATE ล้มเหลว ควรลบแถวที่เพิ่ง INSERT ไปก่อนหน้านี้ด้วย
                     $delete_sql = "DELETE FROM students WHERE id = ?";
                    if ($delete_stmt = $conn->prepare($delete_sql)) {
                         $delete_stmt->bind_param("i", $last_id);
                         $delete_stmt->execute();
                         // ไม่ต้อง close $delete_stmt ที่นี่
                    } else {
                         error_log("Failed to prepare DELETE students with id {$last_id} after update prepare error. Error: {$conn->error}");
                    }
                    throw new Exception("Prepare UPDATE students SQL failed: {$conn->error}");
                }

                $update_stmt->bind_param("si", $student_code, $last_id);
                $update_stmt->execute();
                $update_stmt->close(); // ปิด statement การอัปเดต

                // --- Step 4: เพิ่มข้อมูลเริ่มต้นในตาราง student_info ด้วย student_code ---
                // ตรวจสอบโครงสร้างตาราง student_info: student_code NOT NULL, email NOT NULL
                // จำเป็นต้องใส่ค่าให้กับทั้ง student_code และ email อย่างน้อย
                $insert_info_sql = "INSERT INTO student_info (student_code, email) VALUES (?, ?)";

                try {
                    $insert_info_stmt = $conn->prepare($insert_info_sql);
                    if ($insert_info_stmt === false) {
                        // ถ้า prepare INSERT into student_info ล้มเหลว
                        // ควรลบแถวที่เคยสร้างใน students ไปแล้ว
                        $delete_sql = "DELETE FROM students WHERE id = ?";
                        if ($delete_stmt = $conn->prepare($delete_sql)) {
                             $delete_stmt->bind_param("i", $last_id);
                             $delete_stmt->execute();
                             // ไม่ต้อง close $delete_stmt ที่นี่
                        } else {
                             error_log("Failed to prepare DELETE students with id {$last_id} after student_info prepare error. Error: {$conn->error}");
                        }
                        throw new Exception("Prepare INSERT into student_info failed: {$conn->error}");
                    }

                    // bind parameters: s=student_code (string), s=email (string - ใช้ค่าว่างไปก่อนถ้าไม่มีฟอร์มกรอก email)
                    $dummy_email = ''; // หรือค่า default อื่นๆ ถ้ามี
                    $insert_info_stmt->bind_param("ss", $student_code, $dummy_email);
                    $insert_info_stmt->execute();
                    $insert_info_stmt->close(); // ปิด statement นี้เมื่อเสร็จ

                    // --- Step 5: เพิ่มข้อมูลเริ่มต้นในตาราง students_documents ด้วย student_code ---
                    // ต้องใส่ค่าให้กับคอลัมน์ NOT NULL อื่นๆ ด้วย (id_card_path, parent_guarantee_path, consent_agreement_path)
                    $insert_docs_sql = "INSERT INTO students_documents (student_code, id_card_path, parent_guarantee_path, consent_agreement_path, student_image_path, change_name_path) VALUES (?, ?, ?, ?, ?, ?)";

                    try {
                        $insert_docs_stmt = $conn->prepare($insert_docs_sql);
                        if ($insert_docs_stmt === false) {
                            // ถ้า prepare INSERT into students_documents ล้มเหลว
                            // ควรลบแถวที่เคยสร้างใน students และ student_info ไปแล้ว
                            $delete_sql = "DELETE FROM students WHERE id = ?";
                            if ($delete_stmt = $conn->prepare($delete_sql)) {
                                 $delete_stmt->bind_param("i", $last_id);
                                 $delete_stmt->execute();
                                 // ไม่ต้อง close $delete_stmt ที่นี่
                            } else {
                                 error_log("Failed to prepare DELETE students with id {$last_id} after docs prepare error. Error: {$conn->error}");
                            }
                            // ลบแถวใน student_info ด้วย
                            $delete_info_sql = "DELETE FROM student_info WHERE student_code = ?";
                            if ($delete_info_stmt = $conn->prepare($delete_info_sql)) { // ใช้ชื่อตัวแปรอื่นที่ไม่ซ้ำ
                                 $delete_info_stmt->bind_param("s", $student_code);
                                 $delete_info_stmt->execute();
                                 $delete_info_stmt->close();
                            } else {
                                 error_log("Failed to prepare DELETE student_info with student_code {$student_code} after docs prepare error. Error: {$conn->error}");
                            }

                            throw new Exception("Prepare INSERT into students_documents failed: {$conn->error}");
                        }

                        // ใช้ค่าว่าง '' สำหรับ path ที่ยังไม่มีไฟล์จริง และ NULL สำหรับคอลัมน์ที่อนุญาต NULL (student_image_path, change_name_path)
                        $empty_path = '';
                        $null_path = NULL; // หรือใช้ $empty_path ถ้าคอลัมน์อนุญาตค่าว่างแทน NULL

                        $insert_docs_stmt->bind_param("ssssss",
                            $student_code,
                            $empty_path, // id_card_path (NOT NULL)
                            $empty_path, // parent_guarantee_path (NOT NULL)
                            $empty_path, // consent_agreement_path (NOT NULL)
                            $null_path,  // student_image_path (NULL)
                            $null_path   // change_name_path (NULL)
                        );

                        $insert_docs_stmt->execute();
                        $insert_docs_stmt->close(); // ปิด statement นี้เมื่อเสร็จ

                        // --- การดำเนินการหลังจากลงทะเบียนสำเร็จ และ INSERT ครบทุกตารางที่จำเป็น ---
                        // Redirect ไปหน้า set_password.php พร้อมส่ง id
                        header("Location: ../Public/set_password.php?id=$last_id");
                        exit(); // สำคัญ: ต้องมี exit() หลัง header เพื่อหยุดการทำงานของ script

                    } catch (mysqli_sql_exception $e) {
                         // ดักจับ error เฉพาะตอน insert เข้า students_documents
                         // ควรลบแถวที่เคยสร้างใน students และ student_info ไปแล้ว
                        $delete_sql = "DELETE FROM students WHERE id = ?";
                        if ($delete_stmt = $conn->prepare($delete_sql)) {
                             $delete_stmt->bind_param("i", $last_id);
                             $delete_stmt->execute();
                             // ไม่ต้อง close $delete_stmt ที่นี่
                        } else {
                             error_log("Failed to prepare DELETE students with id {$last_id} after docs DB error. Error: {$conn->error}");
                        }
                        // ลบแถวใน student_info ด้วย
                        $delete_info_sql = "DELETE FROM student_info WHERE student_code = ?";
                        if ($delete_info_stmt = $conn->prepare($delete_info_sql)) {
                             $delete_info_stmt->bind_param("s", $student_code);
                             $delete_info_stmt->execute();
                             $delete_info_stmt->close();
                        } else {
                             error_log("Failed to prepare DELETE student_info with student_code {$student_code} after docs DB error. Error: {$conn->error}");
                        }

                        error_log("Database error inserting into students_documents for student_code {$student_code}: " . $e->getMessage());
                        throw new Exception("Database error inserting into students_documents: " . $e->getMessage());

                    } catch (Exception $e) {
                        // ดักจับ error อื่นๆ (เช่น prepare ล้มเหลวจากการ catch ด้านบน)
                         // ควรลบแถวที่เคยสร้างใน students และ student_info ไปแล้ว
                        $delete_sql = "DELETE FROM students WHERE id = ?";
                        if ($delete_stmt = $conn->prepare($delete_sql)) {
                             $delete_stmt->bind_param("i", $last_id);
                             $delete_stmt->execute();
                             // ไม่ต้อง close $delete_stmt ที่นี่
                        } else {
                             error_log("Failed to prepare DELETE students with id {$last_id} after docs app error. Error: {$conn->error}");
                        }
                        // ลบแถวใน student_info ด้วย
                        $delete_info_sql = "DELETE FROM student_info WHERE student_code = ?";
                        if ($delete_info_stmt = $conn->prepare($delete_info_sql)) {
                             $delete_info_stmt->bind_param("s", $student_code);
                             $delete_info_stmt->execute();
                             $delete_info_stmt->close();
                        } else {
                             error_log("Failed to prepare DELETE student_info with student_code {$student_code} after docs app error. Error: {$conn->error}");
                        }

                        error_log("Application error inserting into students_documents for student_code {$student_code}: " . $e->getMessage());
                        throw new Exception("Application error inserting into students_documents: " . $e->getMessage());
                    }


                } catch (mysqli_sql_exception $e) {
                     // ดักจับ error เฉพาะตอน insert เข้า student_info
                     // ควรลบแถวใน students ที่เพิ่งเพิ่มและอัปเดตไปแล้วด้วย
                    $delete_sql = "DELETE FROM students WHERE id = ?";
                    if ($delete_stmt = $conn->prepare($delete_sql)) {
                         $delete_stmt->bind_param("i", $last_id);
                         $delete_stmt->execute();
                         // ไม่ต้อง close $delete_stmt ที่นี่
                    } else {
                         error_log("Failed to prepare DELETE students with id {$last_id} after student_info DB error. Error: {$conn->error}");
                    }
                    // โยน error ที่ชัดเจนขึ้น
                    error_log("Database error inserting into student_info for student_code {$student_code}: " . $e->getMessage());
                    throw new Exception("Database error inserting into student_info: " . $e->getMessage());

                } catch (Exception $e) {
                    // ดักจับ error อื่นๆ (เช่น prepare ล้มเหลวจากการ catch ด้านบน)
                     // ควรลบแถวใน students ที่เพิ่งเพิ่มและอัปเดตไปแล้วด้วย
                    $delete_sql = "DELETE FROM students WHERE id = ?";
                    if ($delete_stmt = $conn->prepare($delete_sql)) {
                         $delete_stmt->bind_param("i", $last_id);
                         $delete_stmt->execute();
                         // ไม่ต้อง close $delete_stmt ที่นี่
                    } else {
                         error_log("Failed to prepare DELETE students with id {$last_id} after student_info app error. Error: {$conn->error}");
                    }
                    error_log("Application error inserting into student_info for student_code {$student_code}: " . $e->getMessage());
                    throw new Exception("Application error inserting into student_info: " . $e->getMessage());
                }


            } // สิ้นสุด else ของ if ($count > 0) - student_code ไม่ซ้ำ

        } catch (mysqli_sql_exception $e) {
            // ดักจับ Exception ที่เกิดจากฐานข้อมูล (ส่วนใหญ่มาจาก INSERT แรกบน students)
            // Error code 1062 คือ Duplicate entry for key (น่าจะเป็น UNIQUE KEY บน thaiid)
            if ($e->getCode() == 1062) {
                 $error_message = "เลขบัตรประชาชนนี้ได้ถูกใช้ลงทะเบียนแล้ว";
            } else {
                // ข้อผิดพลาดอื่นๆ ที่ไม่ใช่ Duplicate entry จากฐานข้อมูล
                 error_log("Database error during students INSERT: " . $e->getMessage() . " SQL: " . $sql);
                 $error_message = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $e->getMessage();
                 // หรือ throw $e; // สำหรับการพัฒนา เพื่อดูรายละเอียด error เต็มรูปแบบ
            }
        } catch (Exception $e) {
            // ดักจับ Exception อื่นๆ ที่ไม่ใช่จากฐานข้อมูล (เช่น prepare ล้มเหลวจาก INSERT แรกบน students)
            error_log("Application error during students INSERT: " . $e->getMessage());
             $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
        } finally {
            // ปิด statement ที่อาจจะยังเปิดอยู่ เพื่อป้องกัน resource leak
            if ($stmt !== null) {
                $stmt->close();
            }
             // check_stmt ถูกปิดแล้วในบล็อก if/else ด้านบน
            // update_stmt ถูกปิดแล้วในบล็อก if/else ด้านบน
             // insert_info_stmt ถูกปิดแล้วในบล็อก try/catch ด้านบน
             // insert_docs_stmt ถูกปิดแล้วในบล็อก try/catch ด้านบน
             // ตรวจสอบและปิด delete_stmt ถ้าถูกสร้างขึ้น
            if (isset($delete_stmt) && $delete_stmt !== null) {
                $delete_stmt->close();
            }
             // ตรวจสอบและปิด delete_info_stmt ถ้าถูกสร้างขึ้น (ใช้ใน nested catch)
            if (isset($delete_info_stmt) && $delete_info_stmt !== null) {
                 $delete_info_stmt->close();
            }
        }

    } else {
        // กรอกข้อมูลไม่ครบ
        $error_message = "กรุณากรอกข้อมูลให้ครบ";
    }
}
?>

<div class="container">
    <h1>นิสิตใหม่ลงทะเบียนรับรหัสผ่าน</h1>

    <?php if ($error_message): // แสดงข้อความผิดพลาดถ้ามี ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="register_student.php" method="POST">
        <div class="form-group">
            <label for="f_name">ชื่อ</label>
            <input type="text" name="first_name" id="f_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="l_name">นามสกุล</label>
            <input type="text" name="last_name" id="l_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="thaiid">รหัสบัตรประชาชน (Thai ID)</label>
            <input type="text" name="thaiid" id="thaiid" class="form-control" value="<?php echo htmlspecialchars($thaiid); ?>" required>
        </div>

        <button type="submit" name="add_students">ลงทะเบียนนิสิตใหม่</button>
        <a href="../Public/login.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>

</body>
</html>