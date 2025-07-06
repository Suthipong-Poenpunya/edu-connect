<?php
session_start();
if (!isset($_SESSION['student_code'])) {
    header("Location: login.php");
    exit();
}

include('../include/functions.php');

$student_code = $_SESSION['student_code'];
// ดึงข้อมูลนักเรียน
$student_info = getStudentInfo($student_code);

if (!$student_info) {
    echo "ไม่พบข้อมูลนักเรียน";
    exit();
}

$primary_color = "rgb(222, 92, 142)";
$secondary_color = "#FFFFFF";

// ดึงข้อมูลคณะทั้งหมด
$faculties = getAllFaculties();

// ดึงข้อมูลสาขาทั้งหมดเพื่อใช้ใน JavaScript
$allMajors = getAllMajors();
$allMajorsJson = json_encode($allMajors);


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input values
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $faculty_id = intval($_POST['faculty_id']); // Ensure it's an integer
    $major_id = intval($_POST['major_id']);     // Ensure it's an integer
    $year = intval($_POST['year']);           // Ensure it's an integer
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Validate input (add more validation as needed)
    if (empty($email)) {
        $error_message = "กรุณากรอกอีเมล";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "รูปแบบอีเมลไม่ถูกต้อง";
    } else {
        // Update student information in the database
        $update_result = updateStudentInfo(
            $student_code,
            $email,
            $phone_number,
            $faculty_id,
            $major_id,
            $year,
            $date_of_birth,
            $address
        );

        if ($update_result) {
            $success_message = "บันทึกข้อมูลสำเร็จ";
            // Refresh student info
            $student_info = getStudentInfo($student_code);
        } else {
            $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: <?php echo $secondary_color; ?>;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: <?php echo $primary_color; ?>;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 1.1em;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 95%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1.1em;
        }

        .form-group textarea {
            resize: vertical;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            font-size: 1em;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
            font-size: 1em;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background-color: <?php echo $primary_color; ?>;
            color: <?php echo $secondary_color; ?>;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #c84a83;
        }

        .btn-secondary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>แก้ไขข้อมูลส่วนตัว</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" name="email" value="<?php echo isset($student_info['email']) ? htmlspecialchars($student_info['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">เบอร์โทรศัพท์:</label>
                <input type="text" name="phone_number" value="<?php echo isset($student_info['phone_number']) ? htmlspecialchars($student_info['phone_number']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="faculty_id">คณะ:</label>
                <select name="faculty_id" id="faculty_id">
                    <?php foreach ($faculties as $faculty) : ?>
                        <option value="<?php echo $faculty['faculty_id']; ?>" <?php if (isset($student_info['faculty_id']) && $student_info['faculty_id'] == $faculty['faculty_id']) echo 'selected'; ?>>
                            <?php echo $faculty['faculty_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="major_id">สาขาวิชา:</label>
                <select name="major_id" id="major_id">
                    </select>
            </div>

            <div class="form-group">
                <label for="year">ชั้นปี:</label>
                <input type="number" name="year" value="<?php echo isset($student_info['year']) ? htmlspecialchars($student_info['year']) : ''; ?>" min="1" max="4">
            </div>

            <div class="form-group">
                <label for="date_of_birth">วันเกิด:</label>
                <input type="date" name="date_of_birth" value="<?php echo isset($student_info['date_of_birth']) ? htmlspecialchars($student_info['date_of_birth']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="address">ที่อยู่:</label>
                <textarea name="address"><?php echo isset($student_info['address']) ? htmlspecialchars($student_info['address']) : ''; ?></textarea>
            </div>

            <button type="submit" class="btn-primary">บันทึกข้อมูล</button>
            <a href="profile.php" class="btn-secondary">ยกเลิก</a>
        </form>
    </div>

    <script>
    //Prevent form resubmission on refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // JavaScript for dynamic major dropdown filtering
    const allMajors = <?php echo $allMajorsJson; ?>; // Get all majors data from PHP
    const facultySelect = document.getElementById('faculty_id');
    const majorSelect = document.getElementById('major_id');
    const savedMajorId = <?php echo isset($student_info['major_id']) ? json_encode($student_info['major_id']) : 'null'; ?>; // Get the student's saved major_id


    function populateMajors(selectedFacultyId) {
        // Clear current major options
        majorSelect.innerHTML = '';

        // Add a default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '--- เลือกสาขาวิชา ---';
        majorSelect.appendChild(defaultOption);

        // Filter majors based on the first two digits of major_id matching faculty_id
        const filteredMajors = allMajors.filter(major => {
            // Ensure major_id is a string and has at least 2 characters
            if (major.major_id && typeof major.major_id === 'string' && major.major_id.length >= 2) {
                 return major.major_id.substring(0, 2) === selectedFacultyId;
            }
             return false; // Exclude if major_id is invalid
        });

        // Populate the major dropdown with filtered options
        filteredMajors.forEach(major => {
            const option = document.createElement('option');
            option.value = major.major_id;
            option.textContent = major.major_name;
            // Select the saved major if it matches the filtered options
            if (savedMajorId !== null && major.major_id == savedMajorId) {
                 option.selected = true;
            }
            majorSelect.appendChild(option);
        });
    }

    // Event listener for faculty dropdown change
    facultySelect.addEventListener('change', function() {
        const selectedFacultyId = this.value;
        populateMajors(selectedFacultyId);
    });

    // Initial population of major dropdown on page load based on the default selected faculty
    // Or based on the student's saved faculty if available
    const initialFacultyId = facultySelect.value;
    populateMajors(initialFacultyId);

    </script>
</body>
</html>