<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file
// ตรวจสอบให้แน่ใจว่าพาธไปยัง functions.php ถูกต้อง
include '../include/functions.php'; // Include the file containing connectDB() and getStudentDetails()
$admin_name = 'ผู้ดูแลระบบ'; // Default value
$admin_staff_id = '1234'; // Default value
$user_role = null; // Variable to store the fetched role

// --- User Management Logic ---
// Fetching all student details for display using the getStudentDetails() function
// Assuming getStudentDetails() returns an array of student data or false on error
$students = getStudentDetails(); // Use the function that fetches from 'students' table

// Check if getStudentDetails() returned false (indicating an error)
if ($students === false) {
    $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูลนักศึกษาจากฟังก์ชัน";
    $students = []; // Initialize as empty array to prevent errors in the loop
}


// Close database connection
// Note: If connectDB() in functions.php keeps the connection open,
// you might need to adjust connection handling.
// Assuming connectDB() returns the connection and it's closed here.
if (isset($conn) && $conn) {
    $conn->close();
}


// Now $students array contains data from the 'students' table (fetched by the function).
// You will use this array to populate the table in the HTML below.

// --- End User Management Logic ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - Admin Panel</title>
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

        .data-section { /* Use data-section style */
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }

        .data-section h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .action-buttons a {
            margin-right: 5px;
            text-decoration: none;
        }
         .action-buttons form {
             display: inline-block; /* Allow delete button form to be inline */
         }

        /* Style for the search form - Add if needed for user search later */
        .search-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e9e9e9; /* Light grey background for search */
            border-radius: 8px;
            display: flex; /* Use flexbox for layout */
            gap: 10px; /* Space between input and button */
            align-items: center; /* Align items vertically */
        }

        .search-form .form-control {
            flex-grow: 1; /* Allow input to take available space */
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
                    <li><a href="../admin/admin_dashboard.php?staff_id=">หน้าหลัก</a></li>
                    <li><a href="../admin/manage_courses.php">จัดการรายวิชา</a></li>
                    <li><a href="../admin/manage_announcements.php">จัดการประกาศ</a></li>
                    <li><a href="../admin/manage_users.php">จัดการผู้ใช้</a></li>
                    <li><a href="../admin/manage_semesters.php">จัดการภาคการศึกษา</a></li>
                     <li><a href="../admin/21258363_add_admin.php">เพิ่มผู้ใช้ (เจ้าหน้าที่/อาจารย์)</a></li>
                </ul>
            </div>

            <div class="logout-section">
                <a href="logout.php">ออกจากระบบ</a>
            </div>
        </div>

        <div class="admin-main-content">
            <h1>จัดการผู้ใช้ (นิสิต)</h1> <?php
            // Display success or error messages from actions (e.g., from user management action file)
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']); // Clear the message after displaying
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']); // Clear the message after displaying
            }
             if (isset($error_message)): // Display error from fetching students
                echo '<div class="alert alert-danger">' . $error_message . '</div>';
             endif;
            ?>

            <div class="data-section">
                <h2>รายการผู้ใช้ (นิสิต)</h2> <?php if (empty($students)): ?>
                    <p>ไม่พบข้อมูลนักศึกษาในระบบ</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>รหัสนิสิต</th>
                                <th>ชื่อ</th>
                                <th>นามสกุล</th>
                                <th>เลขประจำตัวประชาชน</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                     <td><?php echo htmlspecialchars($student['thaiid']); ?></td>
                                    <td class="action-buttons">
                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                        <form action="../Actions/manage_users_action.php" method="post" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้รายนี้?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
