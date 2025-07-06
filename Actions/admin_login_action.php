<?php
include ('../config/db.php');

// ตรวจสอบและเริ่ม session ถ้ายังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];

    $query = "SELECT id, staff_id, name, role, password FROM users WHERE staff_id = ?"; // ระบุคอลัมน์ที่ต้องการดึง
    $stmt = null; // กำหนดค่าเริ่มต้น

    try {
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
             throw new Exception("เตรียมคำสั่ง SQL ล้มเหลว: " . $conn->error);
        }
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // ใช้ชื่อตัวแปร $user เพื่อความชัดเจน

            // ตรวจสอบรหัสผ่าน
            if (password_verify($password, $user['password'])) {

                // *** ย้ายส่วนตั้งค่า Session มาไว้ที่นี่ ***
                // *** หลังจากตรวจสอบรหัสผ่านและพบผู้ใช้แล้ว ***

                $_SESSION['user_id'] = $user['id']; // เก็บ id (Primary Key)
                $_SESSION['user_role'] = $user['role']; // เก็บ role (ควรเป็น 'Admin' หรือ 'Teachers')
                $_SESSION['user_name'] = $user['name']; // เก็บชื่อ
                $_SESSION['staff_id'] = $user['staff_id']; // เก็บค่า staff_id

                // Redirect ไปยัง dashboard ที่เหมาะสมตามบทบาท
                if ($user['role'] == 'Admin') {
                    // Redirect ไป Admin Dashboard (อาจใช้ session แทนการส่ง staff_id ใน URL เพื่อความปลอดภัย)
                    // header("Location: ../admin/admin_dashboard.php?staff_id=" . urlencode($staff_id)); // เดิม
                    header("Location: ../admin/admin_dashboard.php"); // Redirect โดยไม่ต้องส่ง staff_id ใน URL ถ้า admin_dashboard ดึงจาก session
                     exit();
                } elseif ($user['role'] == 'Teachers') {
                    // Redirect ไป Teacher Dashboard
                    header("Location: ../admin/teacher_dashboard.php");
                    exit();
                } else {
                    // กรณีบทบาทอื่นๆ ที่ไม่รู้จัก
                     $_SESSION['error'] = "บทบาทผู้ใช้ไม่ถูกต้อง";
                     header("Location: ../admin/admin-login.php");
                     exit();
                }

            } else {
                // รหัสผ่านไม่ถูกต้อง
                $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง";
                header("Location: ../admin/admin-login.php"); // Redirect กลับหน้า Login
                exit();
            }
        } else {
            // ไม่พบผู้ใช้ด้วยรหัสเจ้าหน้าที่ที่ระบุ
            $_SESSION['error'] = "ไม่พบรหัสเจ้าหน้าที่นี้ในระบบ";
            header("Location: ../admin/admin-login.php"); // Redirect กลับหน้า Login
            exit();
        }

    } catch (Exception $e) {
         // ดักจับข้อผิดพลาดในการทำงานกับฐานข้อมูลหรืออื่นๆ
         $_SESSION['error'] = "เกิดข้อผิดพลาดในการเข้าสู่ระบบ: " . $e->getMessage();
         header("Location: ../admin/admin-login.php"); // Redirect กลับหน้า Login
         exit();
    } finally {
        // ปิด statement ถ้ามีการเตรียมไว้
        if ($stmt !== null) {
            $stmt->close();
        }
         // ไม่ต้องปิด $conn ที่นี่ เพราะอาจต้องใช้ในหน้าอื่น
    }

} else {
    // ถ้าไม่ได้ Submit แบบ POST แต่เข้าถึงไฟล์นี้โดยตรง
    $_SESSION['error'] = "กรุณาเข้าสู่ระบบผ่านหน้าฟอร์ม";
     header("Location: ../admin/admin-login.php"); // Redirect กลับหน้า Login
     exit();
}

// โค้ดส่วนนี้จะไม่ถูกทำงาน หากมีการ Redirect ในเงื่อนไขด้านบน
?>