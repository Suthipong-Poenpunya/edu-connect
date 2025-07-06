<?php
// ../Actions/teacher_announcements_action.php - Handles teacher's announcement actions (add, edit, delete).

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once '../config/db.php'; // Database connection
include '../include/functions.php'; // Include your functions file

// Check if the user is logged in and is a teacher
$loggedInUserId = $_SESSION['user_id'] ?? null;
$loggedInUserRole = $_SESSION['user_role'] ?? null;
$loggedInStaffId = $_SESSION['staff_id'] ?? null; // รหัสเจ้าหน้าที่/อาจารย์

if ($loggedInUserId === null || $loggedInUserRole !== 'Teachers' || $loggedInStaffId === null) {
    // Redirect to login if not logged in or not a teacher
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header("Location: ../Public/login.php");
    exit();
}

// ตรวจสอบว่าเป็นการร้องขอแบบ POST และมี action ที่กำหนด
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            // Handle Add Announcement (โค้ดเดิมที่เราทำไว้ในขั้นตอนที่ 7)
            $title = $_POST['title'] ?? null;
            $details = $_POST['details'] ?? null;
            $media = $_POST['media'] ?? null; // Optional
            $date = $_POST['date'] ?? null; // Announcement date
            $author_id = $_POST['author_id'] ?? null; // ควรมาจาก hidden input ในฟอร์มสร้าง

            // Basic validation
            // ตรวจสอบว่าข้อมูลครบถ้วนและ author_id ที่ส่งมาตรงกับอาจารย์ที่ล็อกอิน
            if ($title && $details && $date && $author_id === $loggedInStaffId) {
                // Insert into the 'news' table
                // Assuming 'news' table has columns: id (PK), title, details, media, date, author_id, created_at
                $insert_sql = "INSERT INTO news (title, details, media, date, author_id, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

                // *** ใช้ $conn ที่เชื่อมต่อแล้ว ***
                if ($stmt = $conn->prepare($insert_sql)) {
                    // Bind parameters: title(s), details(s), media(s), date(s), author_id(s)
                    $stmt->bind_param("sssss", $title, $details, $media, $date, $author_id);

                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "สร้างประกาศสำเร็จ";
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการสร้างประกาศ: " . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งสร้างประกาศ: " . $conn->error;
                }
            } else {
                 $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการสร้างประกาศ หรือสิทธิ์ผู้ใช้งานไม่ถูกต้อง";
            }

            // Redirect กลับไปหน้าจัดการประกาศ
            header("Location: ../admin/teacher_dashboard.php?page=manage_announcements"); // Redirect ไปหน้า manage_announcements
            exit();
            break;

        case 'edit':
            // Handle Edit Announcement
            $announcement_id = $_POST['announcement_id'] ?? null; // ID ประกาศที่ต้องการแก้ไข
            $title = $_POST['title'] ?? null;
            $details = $_POST['details'] ?? null;
            $media = $_POST['media'] ?? null; // Optional
            $date = $_POST['date'] ?? null; // Announcement date
            // ไม่ต้องรับ author_id จาก Form แก้ไข เพราะเราจะใช้ $loggedInStaffId เพื่อตรวจสอบสิทธิ์

            // Basic validation
            if ($announcement_id && $title && $details && $date) {
                // Update the announcement in the 'news' table
                // *** สำคัญ: ตรวจสอบว่าประกาศนั้นเป็นของอาจารย์ที่ล็อกอินอยู่จริง ***
                $update_sql = "UPDATE news SET title = ?, details = ?, media = ?, date = ? WHERE id = ? AND author_id = ?";

                // *** ใช้ $conn ที่เชื่อมต่อแล้ว ***
                if ($stmt = $conn->prepare($update_sql)) {
                    // Bind parameters: title(s), details(s), media(s), date(s), id(i), author_id(s)
                    $stmt->bind_param("ssssis", $title, $details, $media, $date, $announcement_id, $loggedInStaffId);

                    if ($stmt->execute()) {
                        // ตรวจสอบว่ามีแถวถูก Update จริงหรือไม่ (เพื่อดูว่าประกาศนั้นมีอยู่และเป็นของอาจารย์คนนี้)
                        if ($stmt->affected_rows > 0) {
                            $_SESSION['success_message'] = "แก้ไขประกาศสำเร็จ";
                        } else {
                             // หาก affected_rows เป็น 0 อาจเพราะไม่มีการเปลี่ยนแปลงข้อมูล หรือ ID ไม่ตรงกับอาจารย์คนนี้
                             if ($conn->error) {
                                 $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขประกาศ: " . $conn->error;
                             } else {
                                 $_SESSION['warning_message'] = "ไม่พบประกาศที่ต้องการแก้ไข หรือคุณไม่มีสิทธิ์แก้ไขประกาศนี้";
                             }
                        }
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขประกาศ: " . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งแก้ไขประกาศ: " . $conn->error;
                }
            } else {
                 $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการแก้ไขประกาศ";
            }

            // Redirect กลับไปหน้าจัดการประกาศ
            header("Location: ../admin/teacher_dashboard.php?page=manage_announcements");
            exit();
            break;

        case 'delete':
            // Handle Delete Announcement
            $announcement_id = $_POST['announcement_id'] ?? null; // ID ประกาศที่ต้องการลบ

            // Basic validation
            if ($announcement_id) {
                 // Delete the announcement from the 'news' table
                 // *** สำคัญ: ตรวจสอบว่าประกาศนั้นเป็นของอาจารย์ที่ล็อกอินอยู่จริง ***
                 $delete_sql = "DELETE FROM news WHERE id = ? AND author_id = ?";

                 // *** ใช้ $conn ที่เชื่อมต่อแล้ว ***
                 if ($stmt = $conn->prepare($delete_sql)) {
                    // Bind parameters: id(i), author_id(s)
                     $stmt->bind_param("is", $announcement_id, $loggedInStaffId);

                     if ($stmt->execute()) {
                         // ตรวจสอบว่ามีแถวถูก Delete จริงหรือไม่ (เพื่อดูว่าประกาศนั้นมีอยู่และเป็นของอาจารย์คนนี้)
                         if ($stmt->affected_rows > 0) {
                             $_SESSION['success_message'] = "ลบประกาศสำเร็จ";
                         } else {
                             // หาก affected_rows เป็น 0 อาจเพราะ ID ไม่ตรงกับอาจารย์คนนี้
                              if ($conn->error) {
                                  $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบประกาศ: " . $conn->error;
                              } else {
                                  $_SESSION['warning_message'] = "ไม่พบประกาศที่ต้องการลบ หรือคุณไม่มีสิทธิ์ลบประกาศนี้";
                              }
                         }
                     } else {
                         $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบประกาศ: " . $conn->error;
                     }
                     $stmt->close();
                 } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งลบประกาศ: " . $conn->error;
                 }
            } else {
                 $_SESSION['error_message'] = "ไม่พบข้อมูลประกาศที่ต้องการลบ";
            }

            // Redirect กลับไปหน้าจัดการประกาศ
            header("Location: ../admin/teacher_dashboard.php?page=manage_announcements");
            exit();
            break;

        default:
            // Invalid action
            $_SESSION['error_message'] = "Action ไม่ถูกต้อง";
            header("Location: ../admin/teacher_dashboard.php"); // Redirect ไป dashboard home
            exit();
            break;
    }
} else {
    // ไม่ใช่การร้องขอแบบ POST
    $_SESSION['error_message'] = "การเข้าถึงไม่ถูกต้อง";
    header("Location: ../admin/teacher_dashboard.php"); // Redirect ไป dashboard home
    exit();
}

// ปิดการเชื่อมต่อฐานข้อมูล (ไม่จำเป็น PHP จะปิดอัตโนมัติ)
// $conn->close();
?>