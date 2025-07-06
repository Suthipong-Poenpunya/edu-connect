<?php
// Enable error reporting to help debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['student_code'])) {
    header("Location: login.php");
    exit();
}
// Debugging line to check session variable

// Include the database connection file
include('../include/functions.php');

// Ensure the getStudentSchedule function is included from functions.php

// Get student information
$student_info = getStudentInfo($_SESSION['student_code']);

// Get student schedule
// Assuming $conn is defined in functions.php
$current_schedule = getStudentSchedule($_SESSION['student_code']); // Use the function from functions.php

if ($current_schedule === false) {
    // Handle the error from getStudentSchedule
    echo "Failed to retrieve schedule. Please check the logs.";
    exit();
}

// Array for days of the week in English abbreviation
$daysOfWeek = ['MON', 'TUE', 'WED', 'THU', 'FRI'];
$dayMap = ['MO' => 'MON', 'TU' => 'TUE', 'WE' => 'WED', 'TH' => 'THU', 'FR' => 'FRI'];

// Array for time slots
$timeSlots = [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];

// Set the content type header
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางเรียน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit',sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            color: rgb(222, 92, 142);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        /* ปรับแต่งส่วนหัวของตาราง */
        thead th {
            font-weight: bold;
            background-color: #e0e0e0;
        }

        /* ปรับแต่งช่องแรกของแต่ละแถว (วัน) */
        tbody th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        /* สไตล์สำหรับกล่องรายวิชา */
        .course-box {
            background-color: rgba(222, 92, 142, 0.3); /* สีชมพูอ่อน */
            border: 1px solid rgb(222, 92, 142); /* ขอบสีชมพู */
            padding: 5px;
            border-radius: 4px;
            font-size: 0.8em;
            margin: 2px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%; /* ให้สูงเท่ากับ cell */
            color: #333;
        }

        .course-code {
            font-size: 0.9em;
            font-weight: bold;
        }

        .course-info {
            font-size: 0.75em;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>ตารางเรียน</h1>
        <table>
            <thead>
                <tr>
                    <th>Day/Time</th>
                    <?php foreach ($timeSlots as $time): ?>
                        <th><?php echo $time; ?>:00</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daysOfWeek as $day): ?>
                    <tr>
                        <th><?php echo $day; ?></th>
                        <?php foreach ($timeSlots as $time): ?>
                            <td>
                                <?php
                                $foundCourse = false;
                                if (is_array($current_schedule)) { // Check if $current_schedule is an array
                                    foreach ($current_schedule as $scheduleItem) {
                                        $scheduleDay = isset($dayMap[$scheduleItem['day']]) ? $dayMap[$scheduleItem['day']] : '';
                                        $startTime = 0;
                                        $endTime = 0;

                                        // Parse time
                                        if (preg_match('/^(\d{1,2}):\d{2} - (\d{1,2}):\d{2}$/', $scheduleItem['time'], $matches)) {
                                            $startTime = intval($matches[1]);
                                            $endTime = intval($matches[2]);
                                        } elseif (preg_match('/^(\d{1,2})-(\d{1,2})$/', $scheduleItem['time'], $matches)) {
                                            $startTime = intval($matches[1]);
                                            $endTime = intval($matches[2]);
                                        } elseif (is_numeric(substr($scheduleItem['time'], 0, 2))) {
                                            $startTime = intval(substr($scheduleItem['time'], 0, 2));
                                            if (is_numeric(substr(explode(' - ', $scheduleItem['time'])[1], 0, 2))) {
                                                $endTime = intval(substr(explode(' - ', $scheduleItem['time'])[1], 0, 2));
                                            }
                                        }

                                        // Check if the course is scheduled for this time slot
                                        if ($scheduleDay == $day && ($startTime == $time || ($startTime < $time && $endTime > $time))) {
                                            echo '<div class="course-box">';
                                            echo '<span class="course-code">' . $scheduleItem['course_code'] . '</span>';
                                            echo '<span class="course-info">' . substr($scheduleItem['course_name'], 0, 8) . '</span>';
                                            echo '</div>';
                                            $foundCourse = true;
                                        }
                                    }
                                }
                                if (!$foundCourse) {
                                    echo '&nbsp;';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="dashboard.php">กลับสู่แดชบอร์ด</a></p>
    </div>
</body>
</html>
