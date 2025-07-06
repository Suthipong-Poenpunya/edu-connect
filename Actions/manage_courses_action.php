<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file (if needed for any helper functions, e.g., validation)
include '../include/functions.php'; // Uncomment if you need functions from here

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
            // Redirect back to the manage courses page
            // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
            header("Location: ../admin/manage_courses.php");
            exit();
        }

        switch ($action) {
            case 'add':
                // --- Handle Add Course Action ---
                // Get data from the form
                $course_code = htmlspecialchars($_POST['course_code'] ?? '');
                $course_name = htmlspecialchars($_POST['course_name'] ?? '');
                $credits = htmlspecialchars($_POST['credits'] ?? '');
                $semester = htmlspecialchars($_POST['semester'] ?? '');
                $status = htmlspecialchars($_POST['status'] ?? ''); // Assuming status is a string like 'เปิดสอน' or 'ไม่เปิดสอน'
                $total_seats = htmlspecialchars($_POST['total_seats'] ?? '');
                $available_seats = htmlspecialchars($_POST['available_seats'] ?? '');
                $day = htmlspecialchars($_POST['DAY'] ?? ''); // Note the name 'DAY'
                $time = htmlspecialchars($_POST['TIME'] ?? ''); // Note the name 'TIME'
                $description = htmlspecialchars($_POST['description'] ?? '');

                // Basic validation (you might want more robust validation)
                if (empty($course_code) || empty($course_name) || $credits === '' || empty($semester) || empty($status) || $total_seats === '' || $available_seats === '' || empty($day) || empty($time) || empty($description)) {
                    $_SESSION['error_message'] = "กรุณากรอกข้อมูลรายวิชาให้ครบถ้วน";
                } else {
                    // Prepare SQL query to insert a new course
                    // Ensure column names match your 'courses' table
                    $insert_query = "INSERT INTO courses (course_code, course_name, credits, semester, status, total_seats, available_seats, DAY, TIME, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    // Prepare the statement
                    if ($stmt = $conn->prepare($insert_query)) {
                        // Bind parameters
                        // s: string, i: integer, d: double, b: blob
                        // Adjust types based on your database column types
                        $stmt->bind_param("ssdsiiisss", $course_code, $course_name, $credits, $semester, $status, $total_seats, $available_seats, $day, $time, $description);

                        // Execute the statement
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = "เพิ่มรายวิชาสำเร็จแล้ว";
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มรายวิชา: " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (เพิ่มรายวิชา): " . $conn->error;
                    }
                }
                break;

            case 'edit':
                $course_id = htmlspecialchars($_POST['course_id'] ?? '');
                $course_code = htmlspecialchars($_POST['course_code'] ?? '');
                $course_name = htmlspecialchars($_POST['course_name'] ?? '');
                $credits = htmlspecialchars($_POST['credits'] ?? '');
                $semester = htmlspecialchars($_POST['semester'] ?? '');
                $status = htmlspecialchars($_POST['status'] ?? ''); // Assuming status is a string like 'เปิดสอน' or 'ไม่เปิดสอน'
                $total_seats = htmlspecialchars($_POST['total_seats'] ?? '');
                $available_seats = htmlspecialchars($_POST['available_seats'] ?? '');
                $day = htmlspecialchars($_POST['DAY'] ?? ''); // Note the name 'DAY'
                $time = htmlspecialchars($_POST['TIME'] ?? ''); // Note the name 'TIME'
                $description = htmlspecialchars($_POST['description'] ?? '');

                // Basic validation
                if (empty($course_id) || !is_numeric($course_id) || empty($course_code) || empty($course_name) || $credits === '' || empty($semester) || $status > 1 || $total_seats === '' || $available_seats === '' || empty($day) || empty($time) || empty($description)) {
                    $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการแก้ไขรายวิชา";
                } else {
                    // Prepare SQL query to update the course
                    // Ensure column names match your 'courses' table and the WHERE clause uses the correct ID column
                    $update_query = "UPDATE courses SET course_code = ?, course_name = ?, credits = ?, semester = ?, status = ?, total_seats = ?, available_seats = ?, DAY = ?, TIME = ?, description = ? WHERE id = ?";

                    // Prepare the statement
                    if ($stmt = $conn->prepare($update_query)) {
                        // Bind parameters
                        // s: string, i: integer, d: double, b: blob
                        // Adjust types based on your database column types
                        // Note the order of parameters must match the query, and the last one is the course_id
                        $stmt->bind_param("ssdsiiisssi", $course_code, $course_name, $credits, $semester, $status, $total_seats, $available_seats, $day, $time, $description, $course_id);

                        // Execute the statement
                        if ($stmt->execute()) {
                            // Check if any row was actually updated
                            if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "แก้ไขรายวิชาสำเร็จแล้ว";
                            } else {
                                // This might mean the data was the same, or the ID wasn't found
                                $_SESSION['success_message'] = "ไม่มีการเปลี่ยนแปลงข้อมูล หรือไม่พบรายวิชาที่ต้องการแก้ไข";
                            }
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขรายวิชา: " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (แก้ไขรายวิชา): " . $conn->error;
                    }
                }
                break;

            case 'delete':
                // --- Handle Delete Course Action ---
                // Get the course ID from the form
                $course_id = $_POST['course_id'] ?? null;

                // Basic validation
                if ($course_id === null || !is_numeric($course_id)) {
                    $_SESSION['error_message'] = "ไม่พบรหัสรายวิชาที่ต้องการลบ";
                } else {
                    // Prepare SQL query to delete a course
                    $delete_query = "DELETE FROM courses WHERE id = ?";

                    // Prepare the statement
                    if ($stmt = $conn->prepare($delete_query)) {
                        // Bind parameter (assuming course ID is integer)
                        $stmt->bind_param("i", $course_id);

                        // Execute the statement
                        if ($stmt->execute()) {
                            // Check if any row was actually deleted
                            if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "ลบรายวิชาสำเร็จแล้ว";
                            } else {
                                $_SESSION['error_message'] = "ไม่พบรายวิชาที่ต้องการลบ (อาจถูกลบไปแล้ว)";
                            }
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรายวิชา: " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (ลบรายวิชา): " . $conn->error;
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

    // Redirect back to the manage courses page after processing
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_courses.php");
    exit();

} else {
    // If the request method is not POST, redirect to the manage courses page
    // or an error page
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_courses.php");
    exit();
}
?>
