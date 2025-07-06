<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file
// ตรวจสอบให้แน่ใจว่าพาธไปยัง functions.php ถูกต้อง
include '../include/functions.php'; // Include the file containing connectDB() and getStudentById()

$admin_name = 'ผู้ดูแลระบบ'; // Default value
$admin_staff_id = '1234'; // Default value
$user_role = null; // Variable to store the fetched role
// Now $student_data contains the details of the student to be edited.
// You will use this data to pre-fill the form below.
// --- Edit Student Logic ---
$student_id = $_GET['id'] ?? null; // Get student ID from URL parameter 'id'

$student_data = null; // Variable to store student data

// Check if student ID is provided and is a valid positive integer
if ($student_id === null || !is_numeric($student_id) || $student_id <= 0) {
    $_SESSION['error_message'] = "ไม่พบรหัสผู้ใช้ (นิสิต) ที่ต้องการแก้ไข หรือรหัสไม่ถูกต้อง";
    header("Location: manage_users.php"); // Redirect back to manage users page
    exit();
}

// Fetch student details using the function from functions.php
// This function should now fetch id, student_code, first_name, last_name, thaiid
$student_data = getStudentById($student_id);

// Check if the student was found or if there was a database error
if ($student_data === false) {
     // getStudentById returned false, indicating a DB error
     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการดึงข้อมูลนักศึกษาจากฐานข้อมูล";
    header("Location: manage_users.php"); // Redirect back to manage users page
    exit();
} elseif ($student_data === null) {
     // getStudentById returned null, indicating student not found
     $_SESSION['error_message'] = "ไม่พบนักศึกษาด้วยรหัสที่ระบุ";
    header("Location: manage_users.php"); // Redirect back to manage users page
    exit();
}


// Close database connection (if it's still open from getStudentById)
// Note: If connectDB() in functions.php keeps the connection open,
// you might need to adjust connection handling.
// Assuming connectDB() returns the connection and it's closed here.
if (isset($conn) && $conn) {
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลนิสิต - Admin Panel</title>
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
                    รหัสเจ้าหน้าที่: <?php echo htmlspecialchars($admin_staff_id); ?>
                </div>
            </div>

            <div class="admin-menu">
                <ul>
                    <li><a href="admin_dashboard.php">หน้าหลัก</a></li>
                    <li><a href="manage_courses.php">จัดการรายวิชา</a></li>
                    <li><a href="manage_announcements.php">จัดการประกาศ</a></li>
                    <li><a href="manage_users.php" class="active">จัดการผู้ใช้ (นิสิต)</a></li> <li><a href="manage_semesters.php">จัดการภาคการศึกษา</a></li>
                     <li><a href="add_admin.php">เพิ่มผู้ใช้ (เจ้าหน้าที่/อาจารย์)</a></li>
                </ul>
            </div>

            <div class="logout-section">
                <a href="logout.php">ออกจากระบบ</a>
            </div>
        </div>

        <div class="admin-main-content">
            <h1>แก้ไขข้อมูลนิสิต</h1>

             <?php
            // Display success or error messages from actions (e.g., from manage_users_action.php)
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
                <h2>แก้ไขข้อมูลนิสิต: <?php echo htmlspecialchars($student_data['student_code'] ?? ''); ?></h2>
                <form action="../Actions/manage_users_action.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_data['id'] ?? ''); ?>"> <div class="form-group">
                        <label for="student_code">รหัสนิสิต:</label>
                        <input type="text" class="form-control" id="student_code" name="student_code" value="<?php echo htmlspecialchars($student_data['student_code'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">ชื่อ:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_data['first_name'] ?? ''); ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="last_name">นามสกุล:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_data['last_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="thaiid">เลขประจำตัวประชาชน:</label>
                        <input type="text" class="form-control" id="thaiid" name="thaiid" value="<?php echo htmlspecialchars($student_data['thaiid'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">รหัสผ่าน (ปล่อยว่างหากไม่ต้องการเปลี่ยน):</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="ป้อนรหัสผ่านใหม่หากต้องการเปลี่ยน">
                    </div>

                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                     <a href="manage_users.php" class="btn btn-secondary">ยกเลิก</a> </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
