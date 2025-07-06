<?php
// Start the session
session_start();

// Include the database configuration file
include '/config/db.php';

// Prevent direct access to this file
// Check if the request method is POST and the correct submit button was clicked
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['add_user'])) { // Check for 'add_user' submit button
    // Redirect to an error page or the admin dashboard if accessed directly
    header("Location: ../admin/admin_dashboard.php"); // Redirect to admin dashboard (or an error page)
    exit();
}

// Check if the form was submitted using POST method and the submit button was clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) { // Check for 'add_user' submit button

    // Get form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $staff_id = htmlspecialchars($_POST['staff_id']);
    $password = htmlspecialchars($_POST['password']);
    $role_text = htmlspecialchars($_POST['role']); // Get role text from the dropdown

    // Basic validation (you might want more robust validation)
    if (empty($name) || empty($email) || empty($staff_id) || empty($password) || empty($role_text)) {
        $_SESSION['error_message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        header("Location: /admin/21258363_add_admin.php"); // Redirect back to the form (using add_admin.php as the form page)
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "รูปแบบอีเมลไม่ถูกต้อง";
        header("Location: /admin/a21258363_add_admin.php"); // Redirect back to the form
        exit();
    }

    // Check if email or staff_id already exists (optional but recommended)
    $check_query = "SELECT id FROM users WHERE email = ? OR staff_id = ? LIMIT 1";
    if ($stmt_check = $conn->prepare($check_query)) {
        $stmt_check->bind_param("ss", $email, $staff_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $_SESSION['error_message'] = "อีเมลหรือรหัสเจ้าหน้าที่/อาจารย์นี้มีอยู่ในระบบแล้ว"; // Updated error message
            $stmt_check->close();
            $conn->close();
            header("Location: /admin/21258363_add_admin.php"); // Redirect back to the form
            exit();
        }
        $stmt_check->close();
    } else {
         $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการตรวจสอบข้อมูลซ้ำ: " . $conn->error;
         $conn->close();
         header("Location: /admin/21258363_add_admin.php"); // Redirect back to the form
         exit();
    }

    // Determine the integer value for the role based on the text
    $role_value = null;
    if ($role_text === 'staff') {
        $role_value = 'Admin';
    } elseif ($role_text === 'teacher') {
        $role_value = 'Teachers';
    } else {
        // Handle invalid role value if necessary
        $_SESSION['error_message'] = "บทบาทที่เลือกไม่ถูกต้อง";
        header("Location: /admin/21258363_add_admin.php"); // Redirect back to the form
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to insert new user
    // Assuming your 'users' table has columns: name, email, staff_id, password, role, referral_code (optional)
    // We are inserting name, email, staff_id, hashed_password, and the selected role value (integer)
    // referral_code might be null or generated elsewhere if needed
    $insert_query = "INSERT INTO users (name, email, staff_id, password, role) VALUES (?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($insert_query)) {
        // Bind parameters
        // 'ssssi' indicates that the first four parameters are strings and the last one (role) is an integer
        $stmt->bind_param("sssss", $name, $email, $staff_id, $hashed_password, $role_value); // Bind the integer role value

        // Execute the statement
        if ($stmt->execute()) {
            // Insertion successful
            $_SESSION['success_message'] = "เพิ่มข้อมูลผู้ใช้สำเร็จแล้ว"; // Updated success message
            header("Location: /admin/21258363_add_admin.php"); // Redirect back to the form with success message
            exit();
        } else {
            // Error during insertion
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error;
            header("Location: /admin/21258363_add_admin.php"); // Redirect back with error message
            exit();
        }

        // Close statement
        $stmt->close();
    } else {
        // Error preparing the statement
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล: " . $conn->error;
        header("Location: /admin/21258363_add_admin.php"); // Redirect back with error message
        exit();
    }

    // Close database connection
    $conn->close();

} else {
    // If accessed without POST method or submit button not clicked
    $_SESSION['error_message'] = "การเข้าถึงไม่ถูกต้อง";
    header("Location: /admin/admin_dashboard.php"); // Redirect to admin dashboard (or an error page)
    exit();
}
?>
