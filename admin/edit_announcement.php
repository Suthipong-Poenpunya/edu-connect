<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file
// ตรวจสอบให้แน่ใจว่าพาธไปยัง functions.php ถูกต้อง
include '../include/functions.php'; // Include the file containing connectDB() and getAnnouncementById()
$admin_name = 'ผู้ดูแลระบบ'; // Default value
$staff_id = '1234'; // Default value
$user_role = null; // Variable to store the fetched role
// Check if the user is logged in


// --- Edit Announcement Logic ---
$announcement_id = $_GET['id'] ?? null; // Get announcement ID from URL parameter 'id'

$announcement_data = null; // Variable to store announcement data

// Check if announcement ID is provided and is a valid positive integer
if ($announcement_id === null || !is_numeric($announcement_id) || $announcement_id <= 0) {
    $_SESSION['error_message'] = "ไม่พบรหัสประกาศที่ต้องการแก้ไข หรือรหัสไม่ถูกต้อง";
    header("Location: manage_announcements.php"); // Redirect back to manage announcements page
    exit();
}

// Fetch announcement details using the function from functions.php
// This function should now fetch 'created_at' as the publisher name
$announcement_data = getAnnouncementById($announcement_id);

// Check if the announcement was found or if there was a database error
if ($announcement_data === false) {
     // getAnnouncementById returned false, indicating a DB error
     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการดึงข้อมูลประกาศจากฐานข้อมูล";
    header("Location: manage_announcements.php"); // Redirect back to manage announcements page
    exit();
} elseif ($announcement_data === null) {
     // getAnnouncementById returned null, indicating announcement not found
     $_SESSION['error_message'] = "ไม่พบประกาศด้วยรหัสที่ระบุ";
    header("Location: manage_announcements.php"); // Redirect back to manage announcements page
    exit();
}


// Close database connection (if it's still open from getAnnouncementById)
// Note: If connectDB() in functions.php keeps the connection open,
// you might need to adjust connection handling.
// Assuming connectDB() returns the connection and it's closed here.
if (isset($conn) && $conn) {
    $conn->close();
}


// Now $announcement_data contains the details of the announcement to be edited.
// You will use this data to pre-fill the form below.

// --- End Edit Announcement Logic ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขประกาศ - Admin Panel</title>
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
                    <li><a href="manage_announcements.php" class="active">จัดการประกาศ</a></li> 
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
            <h1>แก้ไขประกาศ</h1>

             <?php
            // Display success or error messages from actions (e.g., from manage_announcements_action.php)
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
                <h2>แก้ไขข้อมูลประกาศ: <?php echo htmlspecialchars($announcement_data['title'] ?? ''); ?></h2>
                <form action="../Actions/manage_announcements_action.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement_data['id'] ?? ''); ?>"> <div class="form-group">
                        <label for="title">หัวข้อประกาศ:</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement_data['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="details">รายละเอียด:</label>
                        <textarea class="form-control" id="details" name="details" rows="5" required><?php echo htmlspecialchars($announcement_data['details'] ?? ''); ?></textarea>
                    </div>
                     <div class="form-group">
                        <label for="date">วันที่:</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($announcement_data['date'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="media">สื่อ (URL หรือ Path):</label>
                        <input type="text" class="form-control" id="media" name="media" value="<?php echo htmlspecialchars($announcement_data['media'] ?? ''); ?>">
                    </div>
                     <div class="form-group">
                        <label for="created_at">ผู้ประกาศ:</label> <input type="text" class="form-control" id="created_at" name="created_at" value="<?php echo htmlspecialchars($announcement_data['created_at'] ?? ''); ?>" placeholder="เช่น งานทะเบียน" required> </div>

                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                     <a href="manage_announcements.php" class="btn btn-secondary">ยกเลิก</a> </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
