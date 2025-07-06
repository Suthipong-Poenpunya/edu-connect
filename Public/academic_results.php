<?php
// academic_results.php - หน้าสำหรับแสดงผลการเรียนของนิสิต

// ตรวจสอบว่ามีการล็อกอินแล้วหรือไม่
session_start();
if (!isset($_SESSION['student_code'])) {
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}
$student_code = $_SESSION['student_code'];

// นำเข้าไฟล์ที่ใช้สำหรับฟังก์ชันที่เกี่ยวข้องกับการดึงข้อมูลจากฐานข้อมูล
include('../include/functions.php'); // สมมติว่าไฟล์ functions.php อยู่ใน folder เดียวกันกับ include

// ดึงข้อมูลผลการเรียนของนิสิตจากฐานข้อมูล
// จะต้องมีฟังก์ชันใน functions.php ที่ทำหน้าที่นี้
// ตัวอย่าง: getAcademicResults($student_code)
$academic_results = getAcademicResults($student_code);

// ดึงข้อมูลนักเรียนสำหรับแสดงผล
$student_info = getStudentInfo($student_code);
$student_data = getStudentData($student_code);
$total_grade_points = 0; // ตัวแปรสำหรับเก็บคะแนนรวม
$total_credits_attempted_all = 0; // ตัวแปรสำหรับหน่วยกิตที่ลงทะเบียนทั้งหมด
$total_credits_gained = 0; // ตัวแปรสำหรับหน่วยกิตที่ได้
$total_credits_attempted_gpa = 0; // ตัวแปรสำหรับหน่วยกิตที่ใช้คำนวณ GPA

// วนลูปข้อมูลผลการเรียนเพื่อคำนวณ
if (!empty($academic_results)) {
    foreach ($academic_results as $result) {
        $credits = (float)$result['credits']; // แปลงหน่วยกิตเป็นตัวเลขทศนิยม
        $grade = trim($result['grade_value']); // ลบช่องว่างหน้าหลังเกรด

        // คำนวณหน่วยกิตทั้งหมดที่ลงทะเบียน (CA)
        // สมมติว่าทุกวิชาที่อยู่ใน academic_results นับเป็นหน่วยกิตที่ลงทะเบียน
        $total_credits_attempted_all += $credits;

        // คำนวณหน่วยกิตที่ได้ (CG)
        // เกรดที่นับเป็นหน่วยกิตที่ได้ (A, B, C, D, S)
        $passing_grades = ['A', 'B', 'C', 'D', 'S'];
        if (in_array(strtoupper($grade), $passing_grades)) {
            $total_credits_gained += $credits;
        }

        // คำนวณสำหรับ GPAX (ใช้เฉพาะเกรดที่มี Grade Point A-F)
        $grade_point = getGradePoint($grade);

        if ($grade_point !== null) { // ตรวจสอบว่าเป็นเกรดที่ใช้คำนวณ GPA ได้หรือไม่
            $total_credits_attempted_gpa += $credits; // นับหน่วยกิตเฉพาะวิชาที่ใช้คำนวณ GPA
            $total_grade_points += ($grade_point * $credits);
        }
    }

    // คำนวณ GPAX
    $gpax = ($total_credits_attempted_gpa > 0) ? ($total_grade_points / $total_credits_attempted_gpa) : 0;
} else {
    // ถ้าไม่มีผลการเรียนเลย ค่าทั้งหมดจะเป็น 0
    $gpax = 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการศึกษา - EDU CU E-Service</title>
    <style>
        @font-face {
        font-family: CHULALONGKORN;
        src: ../fonts/CHULALONGKORNReg.otf;
        }
        @font-face {
        font-family: 'Chulacharas';
        src: url('../images/fonts/ChulaCharasNewReg.ttf') format('truetype');
        }
        h2 {
            font-family: CHULALONGKORN;
            font-size: 2.5em;
            color: rgb(222, 92, 142);
            margin-bottom: 0;
        }
        h1 {
            font-family: CHULALONGKORN;
            font-size: 2.5em;
            color: rgb(222, 92, 142);
            margin-top: 0;
        }
        h3 {
            font-family: Chulacharas;
            font-size: 2em;
            color: rgb(222, 92, 142);
            margin-top: 0;
        }
        body {
            font-family: 'Chulacharas', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 2.5em;
            color: rgb(222, 92, 142);
            margin-bottom: 0;
        }

        .module {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .module h3 {
            color: rgb(222, 92, 142);
            margin-bottom: 15px;
            font-size: 1.8em;
            font-weight: bold;
            border-bottom: 2px solid rgb(222, 92, 142);
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ccc;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #bbb;
        }

        .summary-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .summary-box p {
            margin: 5px 0;
            font-size: 1.1em;
        }

        .summary-box strong {
            color: rgb(222, 92, 142);
        }

    </style>
</head>
<body>
    <div class="container">
        <header>
             <img src="../images/Chula-LOGO.png" alt="Logo" style="width: auto; height: 150px;">
            <h1>จุฬาลงกรณ์มหาวิทยาลัย</h1>
            <h2>ผลการศึกษา</h2>
        </header>

        <div class="module">
            <h3>ชื่อนิสิต: <?php echo htmlspecialchars($student_info['first_name'] . ' ' . $student_info['last_name']); ?>  รหัสนิสิต: <?php echo htmlspecialchars($student_code); ?></h3>

            <?php if (!empty($academic_results)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ปีการศึกษา</th>
                            <th>ภาคเรียน</th>
                            <th>รหัสวิชา</th>
                            <th>ชื่อวิชา</th>
                            <th>หน่วยกิต</th>
                            <th>ผลการเรียน (เกรด)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($academic_results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['academic_year']); ?></td>
                                <td><?php echo htmlspecialchars($result['semester']); ?></td>
                                <td><?php echo htmlspecialchars($result['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($result['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['credits']); ?></td>
                                <td><?php echo htmlspecialchars($result['grade_value']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="summary-box">
                    <h2>สรุปผลการศึกษา</h2>
                    <p><strong>หน่วยกิตสะสมที่ลงทะเบียน (CA):</strong> <?php echo number_format($total_credits_attempted_all, 2); ?></p>
                    <p><strong>หน่วยกิตสะสมที่ได้ (CG):</strong> <?php echo number_format($total_credits_gained, 2); ?></p>
                    <p><strong>แต้มเกรดสะสม (Total Grade Points):</strong> <?php echo number_format($total_grade_points, 2); ?></p>
                    <p><strong>เกรดเฉลี่ยสะสม (GPAX):</strong> <?php echo number_format($gpax, 2); ?></p>
                    <p style="color: #888;">* หมายเหตุ: ข้อมูล GPA รายภาคเรียน, CAX, CGX, GPX (สะสมยกเว้นภาคเรียนปัจจุบัน) ยังไม่ได้คำนวณในหน้านี้</p>
                </div>

            <?php else: ?>
                <p>ไม่พบข้อมูลผลการเรียนสำหรับนิสิตนี้</p>
            <?php endif; ?>

            <a href="dashboard.php" class="back-button">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</body>
<footer>
    <div class="container">
        <p style="text-align: center; color: #888;">&copy; 2024 จุฬาลงกรณ์มหาวิทยาลัย สงวนลิขสิทธิ์.</p>
    </div>
</footer>
</html>