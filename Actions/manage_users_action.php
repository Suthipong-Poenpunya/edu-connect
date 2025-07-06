<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file (if needed, though not strictly necessary for a simple delete)
include '../include/functions.php'; // Uncomment if you need helper functions here

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if the 'action' parameter is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Get database connection
        $conn = connectDB(); // Assuming connectDB() function is available and returns connection

        // Check if database connection is successful
        if (!$conn) {
            $_SESSION['error_message'] = "ไม่สามารถเชื่อมต่อฐานข้อมูลเพื่อดำเนินการได้";
            // Redirect back to the manage users page
            // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
            header("Location: ../admin/manage_users.php");
            exit();
        }

        switch ($action) {
            case 'add':
                // --- Handle Add User Action (for students) ---
                // Get data from the form (from manage_users.php)
                $student_code = $_POST['student_code'] ?? null;
                $first_name = $_POST['first_name'] ?? null;
                $last_name = $_POST['last_name'] ?? null;
                $thaiid = $_POST['thaiid'] ?? null;
                $password = $_POST['password'] ?? null;

                // Basic validation
                if ($student_code === null || $first_name === null || $last_name === null || $thaiid === null || $password === null) {
                    $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการสร้างผู้ใช้ (นิสิต)";
                } else {
                    // Prepare SQL query to insert a new student into the 'students' table
                    // Adjust columns as per your database table structure
                    $insert_query = "INSERT INTO students (student_code, first_name, last_name, thaiid, password) VALUES (?, ?, ?, ?, ?)";

                    // Prepare the statement
                    if ($stmt = $conn->prepare($insert_query)) {
                        // Bind parameters (assuming all are strings)
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password for security
                        $stmt->bind_param("sssss", $student_code, $first_name, $last_name, $thaiid, $hashed_password);

                        // Execute the statement
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = "เพิ่มผู้ใช้ (นิสิต) สำเร็จแล้ว";
                        } else {
                            // Check for duplicate entry error (e.g., duplicate student_code)
                            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                                $_SESSION['error_message'] = "ไม่สามารถเพิ่มผู้ใช้ได้: รหัสนิสิต " . htmlspecialchars($student_code) . " มีอยู่ในระบบแล้ว";
                            } else {
                                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้ (นิสิต): " . $stmt->error;
                            }
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (เพิ่มผู้ใช้ นิสิต): " . $conn->error;
                    }
                }
                break;

            case 'edit':
                // --- Handle Edit User Action (for students) ---
                // Get the student ID and other data from the form (from manage_users.php)
                $student_code = $_POST['student_code'] ?? null; // Use student_code as the identifier for editing
                $first_name = $_POST['first_name'] ?? null;
                $last_name = $_POST['last_name'] ?? null;
                $thaiid = $_POST['thaiid'] ?? null;
                $password = $_POST['password'] ?? null; // New password if provided

                // Basic validation
                if ($student_code === null) {
                    $_SESSION['error_message'] = "ไม่พบรหัสนิสิตที่ต้องการแก้ไข";
                } else {
                    // Start building the UPDATE query
                    $update_query = "UPDATE students SET first_name = ?, last_name = ?, thaiid = ?";
                    $params = [$first_name, $last_name, $thaiid];
                    $types = "sss";

                    // Check if password is provided for update
                    if ($password !== null && $password !== '') {
                        $update_query .= ", password = ?";
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $params[] = $hashed_password;
                        $types .= "s";
                    }

                    // Add the WHERE clause
                    $update_query .= " WHERE student_code = ?";
                    $params[] = $student_code;
                    $types .= "s";

                    // Prepare the statement
                    if ($stmt = $conn->prepare($update_query)) {
                        // Bind parameters
                        $stmt->bind_param($types, ...$params);

                        // Execute the statement
                        if ($stmt->execute()) {
                             if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "แก้ไขข้อมูลผู้ใช้ (นิสิต) สำเร็จแล้ว";
                            } else {
                                // This might mean the student_code wasn't found or no data was changed
                                $_SESSION['error_message'] = "ไม่พบผู้ใช้ (นิสิต) ที่ต้องการแก้ไข หรือไม่มีข้อมูลเปลี่ยนแปลง";
                            }
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูลผู้ใช้ (นิสิต): " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (แก้ไขผู้ใช้ นิสิต): " . $conn->error;
                    }
                }
                break;

            case 'delete':
                // --- Handle Delete User Action (for students) ---
                // Get the student ID from the form (from manage_users.php table)
                $student_id = $_POST['student_id'] ?? null; // Name from the hidden input in the delete form

                // Basic validation
                if ($student_id === null || !is_numeric($student_id) || $student_id <= 0) {
                    $_SESSION['error_message'] = "ไม่พบรหัสผู้ใช้ (นิสิต) ที่ต้องการลบ หรือรหัสไม่ถูกต้อง";
                } else {
                    // Prepare SQL query to delete a student from the 'students' table
                    // Ensure the WHERE clause uses the correct ID column (assuming 'id')
                    $delete_query = "DELETE FROM students WHERE id = ?"; // Delete based on 'id' column

                    // Prepare the statement
                    if ($stmt = $conn->prepare($delete_query)) {
                        // Bind parameter (assuming student ID is integer)
                        $stmt->bind_param("i", $student_id);

                        // Execute the statement
                        if ($stmt->execute()) {
                            // Check if any row was actually deleted
                            if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "ลบผู้ใช้ (นิสิต) สำเร็จแล้ว";
                            } else {
                                // This might mean the ID wasn't found (already deleted?)
                                $_SESSION['error_message'] = "ไม่พบผู้ใช้ (นิสิต) ที่ต้องการลบ (อาจถูกลบไปแล้ว)";
                            }
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบผู้ใช้ (นิสิต): " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (ลบผู้ใช้ นิสิต): " . $conn->error;
                    }
                }
                break;

            default:
                // Handle unknown action
                $_SESSION['error_message'] = "การดำเนินการไม่ถูกต้อง";
                break;
        }

        // Close database connection after all operations are done
        if (isset($conn) && $conn) {
            $conn->close();
        }

    } else {
        // 'action' parameter is not set
        $_SESSION['error_message'] = "ไม่ระบุการดำเนินการ";
    }

    // Redirect back to the manage users page after processing
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_users.php");
    exit();

} else {
    // If the request method is not POST, redirect to the manage users page
    // or an error page
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_users.php");
    exit();
}
?>
