<?php
session_start(); // Start the session to access session variables
// Include the database configuration file
include '../config/db.php'; 


$user_id = $_SESSION['staff_id'] ?? null; // Get user_id from GET request or set to null if not provided
$admin_name = 'ผู้ดูแลระบบ'; // Default value
$admin_staff_id = '1234'; // Default value
$user_role = null; // Variable to store the fetched role
$id = null;
$name = null;
$email = null;
$referral_code = null;
$password = null; // Be cautious with password data
$role = null;
$staff_id = null;

// Check if user_id is available
if ($user_id !== null) {
    // Prepare a SQL query to select user data from the 'users' table by ID
    // Adjust the query and column names if your table structure is different
    $query = "SELECT id, name, email, referral_code, password, role, staff_id FROM users WHERE staff_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the user ID parameter (assuming ID is integer, use 's' if it's a string like staff_id)
        // Based on your table structure having 'id' as the primary key, 'i' is likely correct.
        $stmt->bind_param("i", $user_id); // 'i' for integer

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if a user was found
        if ($result->num_rows == 1) {
            // Fetch user data
            $user = $result->fetch_assoc();

            // Assign fetched data to variables
            $id = $user['id'];
            $name = $user['name'];
            $email = $user['email'];
            $referral_code = $user['referral_code'];
            $password = $user['password']; // Again, be careful with displaying/handling password
            $role = $user['role'];
            $staff_id = $user['staff_id'];

            // Now, variables like $name, $email, $role, etc., contain the user's data.
            // You can use these variables to display information on the page.

        } else {
            
        }

        // Close statement
        $stmt->close();
    } else {
        // Error preparing the statement
        // Log this error in a real application
        echo "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล: " . $conn->error;
    }

    // Close database connection (if it's not needed elsewhere on the page)
    // $conn->close();
} else {
    // User ID was not provided in the GET parameter
    // You might want to handle this case, e.g., redirect or show a message
}

$POST['staff_id'] = $staff_id; // Store staff_id in POST array for later use
// Note: The main content area below is a placeholder.
// In a real application, clicking menu links would load content dynamically
// into this area using JavaScript/AJAX, or redirect to separate pages
// for managing courses, announcements, users, etc.
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EDU CU E-Service</title>
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

        .admin-menu a:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent white on hover */
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

        .welcome-box {
            background-color: #f0f0f0; /* Light grey background */
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #F05023; /* Theme color accent */
        }

        .welcome-box h3 {
            color: #333;
            margin-top: 0;
        }
         .alert {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <h2>ระบบจัดการข้อมูล สำหรับเจ้าหน้าที่</h2>
                <div class="admin-info">
                    ยินดีต้อนรับ, <?php echo htmlspecialchars($name); ?><br>
                    รหัสเจ้าหน้าที่: <?php echo htmlspecialchars($user_id); ?>
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
                <a href="../Public/logout.php">ออกจากระบบ</a>
            </div>
        </div>

        <div class="admin-main-content">
            <h1>ยินดีต้อนรับ</h1>

             <?php
            // Display success or error messages from other admin actions if any
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']); // Clear the message after displaying
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']); // Clear the message after displaying
            }
            ?>

            <div class="welcome-box">
                <h3>ยินดีต้อนรับสู่ระบบจัดการ EDU CU E-Service</h3>
                <p>คุณสามารถใช้เมนูทางด้านซ้ายเพื่อจัดการข้อมูลต่างๆ ในระบบ เช่น รายวิชา ประกาศ และข้อมูลผู้ใช้</p>
                <p>โปรดใช้ความระมัดระวังในการแก้ไขข้อมูล</p>
            </div>

            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
