<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // ปรับพาธตามโครงสร้างโปรเจกต์ของคุณ

// Include the functions file
// ตรวจสอบให้แน่ใจว่าพาธไปยัง functions.php ถูกต้อง
include '../include/functions.php'; // รวมไฟล์ที่มี connectDB() และ getCourseDetailsById()
$admin_staff_id = $_SESSION['staff_id'] ?? 1234; // Get admin staff ID from session
$user_id = $_SESSION['user_id'] ?? null; // Get user ID from session
$admin_name = $_SESSION['name'] ?? 'ผู้ดูแลระบบ'; // Get admin name from session

// --- Edit Course Logic ---
$course_id = $_GET['id'] ?? null; // Get course ID from URL parameter 'id'

$course_data = null; // Variable to store course data

// Check if course ID is provided and is a valid positive integer
if ($course_id === null || !is_numeric($course_id) || $course_id <= 0) {
    $_SESSION['error_message'] = "ไม่พบรหัสรายวิชาที่ต้องการแก้ไข หรือรหัสไม่ถูกต้อง";
    header("Location: manage_courses.php"); // Redirect back to manage courses page
    exit();
}

// Fetch course details using the function from functions.php
$course_data = getCourseDetailsById($course_id);

// Check if the course was found or if there was a database error
if ($course_data === false) {
     // getCourseDetailsById returned false, indicating a DB error
     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการดึงข้อมูลรายวิชาจากฐานข้อมูล";
    header("Location: manage_courses.php"); // Redirect back to manage courses page
    exit();
} elseif ($course_data === null) {
     // getCourseDetailsById returned null, indicating course not found
     $_SESSION['error_message'] = "ไม่พบรายวิชาด้วยรหัสที่ระบุ";
    header("Location: manage_courses.php"); // Redirect back to manage courses page
    exit();
}


// Close database connection (if it's still open from getCourseDetailsById)
// Note: If connectDB() in functions.php keeps the connection open,
// you might need to adjust connection handling.
// Assuming connectDB() returns the connection and it's closed here.
if (isset($conn) && $conn) {
    $conn->close();
}

// Now $course_data contains the details of the course to be edited.
// You will use this data to pre-fill the form below.

// --- End Edit Course Logic ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขรายวิชา - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex; /* Use Flexbox for sidebar and main content */
            width: 95%; /* Adjust width as needed */
            max-width: 1400px; /* Max width for larger screens */
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden; /* Hide overflowing content */
            min-height: 80vh; /* Minimum height for the dashboard */
        }

        .admin-sidebar {
            flex: 0 0 220px; /* Fixed width for sidebar */
            background-color: #F05023; /* Theme color */
            color: #fff; /* White text */
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px; /* Space between sidebar sections */
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3); /* Light border */
        }

        .sidebar-header h2 {
            color: #fff;
            font-size: 1.5em;
            margin: 0;
        }

        .admin-info {
            font-size: 0.9em;
            color: rgba(255, 255, 255, 0.8);
        }


        .admin-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .admin-menu li {
            margin-bottom: 10px;
        }

        .admin-menu a {
            text-decoration: none;
            color: #fff; /* White link color */
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .admin-menu a:hover,
        .admin-menu a.active { /* Active state for current page */
            background-color: rgba(255, 255, 255, 0.3); /* More transparent white on hover/active */
        }


        .logout-section {
            margin-top: auto; /* Push logout to the bottom */
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.3); /* Light border */
        }

        .logout-section a {
             text-decoration: none;
            color: #fff; /* White link color */
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1); /* Slightly different background */
            text-align: center;
             transition: background-color 0.3s ease;
        }

         .logout-section a:hover {
            background-color: rgba(255, 255, 255, 0.3); /* More transparent white on hover */
         }


        .admin-main-content {
            flex-grow: 1; /* Main content takes remaining space */
            padding: 20px;
            background-color: #fff; /* White background */
            overflow-y: auto; /* Add scroll if content is long */
        }

        .admin-main-content h1 {
            color: #333; /* Dark text for main heading */
            margin-bottom: 20px;
        }

         .alert {
            margin-bottom: 20px;
        }

        .form-section { /* Use form-section style */
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }

        .form-section h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-primary, .btn-secondary { /* Include secondary button style */
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #F05023; /* Theme color */
            color: #fff;
        }
         .btn-primary:hover {
            background-color: #d4451f; /* Darker theme color */
         }

        .btn-secondary { /* Style for the cancel button */
            background-color: #6c757d; /* Bootstrap secondary color */
            color: #fff;
        }
         .btn-secondary:hover {
            background-color: #5a6268;
         }


    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <div class="admin-info">
                    ยินดีต้อนรับ, <?php echo htmlspecialchars($admin_name); ?><br>
                    <strong>ผู้ดูแลระบบ</strong><br>
                    รหัสเจ้าหน้าที่: <?php echo htmlspecialchars($admin_staff_id); ?> <br>
                    <strong>รหัสผู้ใช้: <?php echo htmlspecialchars($user_id); ?></strong>
                </div>
            </div>

            <div class="admin-menu">
                <ul>
                    <li><a href="admin_dashboard.php">หน้าหลัก</a></li>
                    <li><a href="manage_courses.php" class="active">จัดการรายวิชา</a></li> <li><a href="manage_announcements.php">จัดการประกาศ</a></li>
                    <li><a href="manage_users.php">จัดการผู้ใช้</a></li>
                    <li><a href="manage_semesters.php">จัดการภาคการศึกษา</a></li>
                     <li><a href="add_admin.php">เพิ่มผู้ใช้ (เจ้าหน้าที่/อาจารย์)</a></li>
                </ul>
            </div>

            <div class="logout-section">
                <a href="logout.php">ออกจากระบบ</a>
            </div>
        </div>

        <div class="admin-main-content">
            <h1>แก้ไขรายวิชา</h1>

             <?php
            // Display success or error messages from actions (e.g., from manage_courses_action.php)
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']); // Clear the message after displaying
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']); // Clear the message after displaying
            }
            ?>

            <div class="form-section">
                <h2>แก้ไขข้อมูลรายวิชา: <?php echo htmlspecialchars($course_data['course_code'] ?? ''); ?></h2>
                <form action="../Actions/manage_courses_action.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_data['id'] ?? ''); ?>"> <div class="form-group">
                        <label for="course_code">รหัสวิชา:</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo htmlspecialchars($course_data['course_code'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="course_name">ชื่อวิชา:</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_data['course_name'] ?? ''); ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="credits">หน่วยกิต:</label>
                        <input type="number" class="form-control" id="credits" name="credits" value="<?php echo htmlspecialchars($course_data['credits'] ?? ''); ?>" required min="0.5" step="0.5">
                    </div>
                     <div class="form-group">
                        <label for="semester">ภาคการศึกษา (เช่น 2567/1):</label>
                        <input type="text" class="form-control" id="semester" name="semester" value="<?php echo htmlspecialchars($course_data['semester'] ?? ''); ?>" placeholder="เช่น 2567/1" required>
                    </div>
                    <div class="form-group">
                        <label for="status">สถานะ:</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">เลือกสถานะ</option>
                            <option value="1" <?php if (($course_data['status'] ?? '') === 'เปิดสอน') echo 'selected'; ?>>เปิดสอน</option>
                            <option value="0" <?php if (($course_data['status'] ?? '') === 'ปิดแล้ว') echo 'selected'; ?>>ปิดแล้ว</option>
                            <option value="0" <?php if (($course_data['status'] ?? '') === 'ยกเลิก') echo 'selected'; ?>>ยกเลิก</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="total_seats">จำนวนที่นั่งทั้งหมด:</label>
                        <input type="number" class="form-control" id="total_seats" name="total_seats" value="<?php echo htmlspecialchars($course_data['total_seats'] ?? ''); ?>" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="available_seats">จำนวนที่นั่งที่เหลือ:</label>
                        <input type="number" class="form-control" id="available_seats" name="available_seats" value="<?php echo htmlspecialchars($course_data['available_seats'] ?? ''); ?>" required min="0">
                    </div>
                     <div class="form-group">
                        <label for="day">วัน:</label>
                        <input type="text" class="form-control" id="DAY" name="DAY" value="<?php echo htmlspecialchars($course_data['DAY'] ?? ''); ?>" placeholder="เช่น จันทร์" required>
                    </div>
                     <div class="form-group">
                        <label for="time">เวลา:</label>
                        <input type="text" class="form-control" id="time" name="TIME" value="<?php echo htmlspecialchars($course_data['TIME'] ?? ''); ?>" placeholder="เช่น 09:00 - 12:00" required>
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียดรายวิชา:</label>
                        <textarea class="form-control" id="description" name="description" placeholder="รายละเอียด" required rows="3"><?php echo htmlspecialchars($course_data['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                     <a href="manage_courses.php" class="btn btn-secondary">ยกเลิก</a> </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
