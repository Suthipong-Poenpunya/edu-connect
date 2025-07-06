<?php
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
include('../include/functions.php');




// ดึงข้อมูลนักเรียนจากฐานข้อมูล
// โดยใช้ฟังก์ชัน getStudentInfo() จากไฟล์ functions.php
$student_info = getStudentInfo($student_code);
$student_data = getStudentData($student_code); // ดึงข้อมูลนักเรียนจากฐานข้อมูล

// ดึงรายวิชาที่เปิดสอน
// โดยใช้ฟังก์ชัน getAvailableCourses() จากไฟล์ functions.php
$available_courses = getAvailableCourses();

$faculties = getFaculties($student_code); // ดึงข้อมูลคณะจากฐานข้อมูล
$majors = getMajors($student_code); // ดึงข้อมูลสาขาจากฐานข้อมูล

// ดึงตารางเรียนปัจจุบันของนักเรียน
// โดยใช้ฟังก์ชัน getCurrentSchedule() จากไฟล์ functions.php
$current_schedule = getCurrentSchedule($student_code);

$news_items = [];
$sql_news = "SELECT * FROM news ORDER BY id DESC LIMIT 10"; // ดึง 10 ข่าวล่าสุด, สมมติมีคอลัมน์ created_at
$result_news = $conn->query($sql_news);

if ($result_news && $result_news->num_rows > 0) {
    while($row = $result_news->fetch_assoc()) {
        $news_items[] = $row;
    }
}
$profile_pic = getStudentProfilePicture($student_code);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDU CU Conncet</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        /* CSS สำหรับจัดแต่งหน้าตาของเว็บเพจ */
        body {
            font-family: 'Kanit', sans-serif;
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
            display: flex; /* ทำให้องค์ประกอบภายใน header เป็น Flex items */
            align-items: center; /* จัดองค์ประกอบภายใน header ให้อยู่กึ่งกลางในแนวตั้ง */
            justify-content: center; /* จัดองค์ประกอบภายใน header ให้อยู่กึ่งกลางในแนวนอน */
            /* คุณอาจจะต้องปรับ margin หรือ padding ของ img และ h1 เพิ่มเติมเพื่อให้ได้ระยะห่างที่ต้องการ */
        }

        header img {
            /* สไตล์เดิมของ img */
            width: 300px;
            height: auto;
            margin-right: 20px; /* เพิ่มระยะห่างด้านขวาของโลโก้ */
        }

        header h1 {
            /* สไตล์เดิมของ h1 */
            font-size: 2.5em;
            color: rgb(222, 92, 142);
            margin: 0; /* ลบ margin เริ่มต้นของ h1 ออก เพื่อไม่ให้มีผลกับการจัดกึ่งกลางของ Flexbox */
        }

        .row {
            display: flex;
            flex-wrap: wrap; /* อนุญาตให้ items ขึ้นบรรทัดใหม่เมื่อพื้นที่ไม่พอ */
            margin-top: 20px;
            justify-content: space-between; /* จัดให้ items มีช่องว่างระหว่างกัน */
        }

        .module {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 48%; /* กำหนดความกว้างให้แต่ละ module ประมาณครึ่งหนึ่งของ container */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box; /* รวม padding และ border เข้าไปใน width */
        }

        /* CSS สำหรับ module ข่าวสารโดยเฉพาะ */
        .news-module {
             width: 48%; /* ใช้ความกว้างเท่ากับ module อื่นๆ */
             /* อาจเพิ่ม styling เฉพาะสำหรับ module ข่าวสารที่นี่ */
        }


        .module:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .module h2 {
            color: rgb(222, 92, 142);
            margin-bottom: 15px;
            font-size: 1.8em;
            font-weight: bold;
            border-bottom: 2px solid rgb(222, 92, 142);
            padding-bottom: 10px;
        }

        .module p {
            margin-bottom: 15px;
            font-size: 1.1em;
            line-height: 1.7;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: rgb(222, 92, 142);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.2em;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .button:hover {
            background-color: rgb(194, 80, 122);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

        /* CSS สำหรับรายการข่าวสาร */
        .news-item {
            border-bottom: 1px dashed #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .news-item h3 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #333;
            font-size: 1.3em;
        }

        .news-item p {
            margin-bottom: 5px;
            font-size: 1em;
            color: #555;
        }

        .news-item .news-date {
            font-size: 0.9em;
            color: #888;
            text-align: right;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .row {
                flex-direction: column; /* จัดเรียง items เป็นคอลัมน์เดียวบนจอเล็ก */
            }
            .module, .news-module {
                width: 100%; /* ให้ module กว้างเต็มจอ */
            }
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            float: right;
            margin-right: 20px; /* Adds some space on the right */
            margin-top: 20px; /* Keep top margin */
            margin-bottom: 20px; /* Keep bottom margin */
            margin: 20px auto;
            border: 5px solid rgb(222, 92, 142);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .register-button {
            margin: 20px 0;
            display: inline-block;
            padding: 12px 25px;
            background-color: rgb(222, 92, 142);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.2em;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .register-button:hover {
            background-color: rgb(194, 80, 122);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .logout {
            
            position: relative;
            margin: 20px 0;
            display: inline-block;
            padding: 12px 25px;
            background-color: rgb(222, 92, 142);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.2em;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            left: 1045px;

        }
        .logout:hover {
            background-color: rgb(194, 80, 122);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>

<body>
    <div class="container">
        <header>
            <img src="../images/EDU-Logo.png" alt="Logo"">
            <h1 style="font-size: 2.5em; color: rgb(222, 92, 142);">EDU CU Conncet</h1>
        </header>

        <div class="row">
            <div class="module">
                <h2>ข้อมูลนิสิต</h2>
                    <div class="profile-picture">
                        <?php
                        // กำหนด path ไปยังรูปภาพ
                        $image_path = "../images/student_image/" . $student_data['student_code'] . ".png"; // เปลี่ยน .jpg เป็น .png หรือนามสกุลไฟล์ที่ถูกต้อง

                        // ตรวจสอบว่าไฟล์ภาพมีอยู่จริงหรือไม่
                        if (file_exists($image_path)) {
                            echo '<img src="' . $image_path . '" alt="รูปภาพนิสิต">';
                        } else {
                            // แสดงภาพ default ถ้าไม่มีภาพของนิสิต
                            echo '<img src="../images/default.jpg" alt="รูปภาพนิสิต">'; // ต้องมีไฟล์ default.jpg ใน folder images
                        }
                        ?>
                    </div>
                <?php if ($student_info) : ?>
                    <p><strong>รหัสนิสิต:</strong> <?php echo htmlspecialchars($student_code); ?></p>
                    <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($student_info['first_name']); ?></p>
                    <p><strong>นามสกุล:</strong> <?php echo htmlspecialchars($student_info['last_name']); ?></p>
                    <p><strong>คณะ:</strong> <?php echo htmlspecialchars($student_data['faculty_name']); ?></p>
                    <p><strong>สาขา:</strong> <?php echo htmlspecialchars($student_data['major_name']); ?></p>
                <?php else : ?>
                    <p>ไม่พบข้อมูลนักเรียน</p>
                <?php endif; ?>
            </div>

            <div class="module news-module">
                <h2>ประกาศข่าวสาร</h2>
                <?php if (!empty($news_items)) : ?>
                    <?php foreach ($news_items as $news) : ?>
                        <div class="news-item">
                            <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($news['details'])); ?></p>
                            <?php if (isset($news['created_at'])): ?>
                                <div class="news-date">เผยแพร่: <?php echo htmlspecialchars($news['created_at']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>ยังไม่มีประกาศข่าวสารในขณะนี้</p>
                <?php endif; ?>
            </div>

             <div class="module">
                <h2>รายวิชาที่เปิดสอน</h2>
                <?php if (!empty($available_courses)) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>รหัสวิชา</th>
                                <th>ชื่อวิชา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($available_courses as $course) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="register_courses.php" class="register-button">ลงทะเบียนเรียน</a>
                <?php else : ?>
                    <p>ยังไม่มีรายวิชาที่เปิดสอนในขณะนี้</p>
                <?php endif; ?>
            </div>

            <div class="module">
                <h2>ตารางเรียนปัจจุบัน</h2>
                <?php if (!empty($current_schedule)) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>วัน</th>
                                <th>เวลา</th>
                                <th>รหัสวิชา</th>
                                <th>ชื่อวิชา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_schedule as $schedule) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['day']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['time']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['course_name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>ยังไม่มีตารางเรียนในขณะนี้</p>
                <?php endif; ?>
            </div>
            <div class="module">
                <h2>ผลการศึกษา</h2>
                <p>ดูผลการเรียนย้อนหลังทั้งหมดของคุณ</p>
                <a href="academic_results.php" class="button">ดูผลการศึกษา</a>
            </div>
            <div class="module">
                <h2>ข้อมูลส่วนตัว</h2>
                <p>ดูและแก้ไขข้อมูลส่วนตัวของคุณ</p>
                <a href="profile.php" class="button">ดูข้อมูลส่วนตัว</a>
            </div>
            <?php
            // Check the first two digits of the student code
            if (substr($student_code, 0, 2) == '67') : ?>
                <div class="module">
                    <h2>ลงทะเบียนแรกเข้า</h2>
                    <p>ลงทะเบียนแรกเข้าสำหรับนิสิตใหม่ รหัส 67</p>
                    <a href="freshmen_register.php" class="button">ลงทะเบียนแรกเข้า</a>
                </div>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="logout">ออกจากระบบ</a>
    </div>
</body>
<footer>
    <div class="container">
        <p style="text-align: center; color: #888;">&copy; 2024 จุฬาลงกรณ์มหาวิทยาลัย สงวนลิขสิทธิ์.</p>
    </div>

</html>
