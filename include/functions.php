<?php
// **สำคัญ:** นี่เป็นตัวอย่างฟังก์ชันสมมติ คุณจะต้องเขียนโค้ดเชื่อมต่อฐานข้อมูลจริงและดึงข้อมูลตามโครงสร้างฐานข้อมูลของคุณ

$host = "localhost";
$user = "root";
$password = "";
$database = "edu e-service";
$conn = new mysqli($host, $user, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ตั้งค่า charset เป็น utf8mb4 เพื่อรองรับภาษาไทยและอีโมจิ
$conn->set_charset("utf8mb4");

function connectDB()
{
    global $host, $user, $password, $database, $conn; // เปลี่ยน $username เป็น $user
    if (!$conn) {
        $conn = mysqli_connect($host, $user, $password, $database); // เปลี่ยน $username เป็น $user และ $dbname เป็น $database
        if (mysqli_connect_errno()) {
            die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, "utf8"); // Set character set to UTF-8
    }
    return $conn;
}

function getStudentInfo($student_code)
{
    $conn = connectDB();
    $sql = "SELECT first_name, last_name FROM students WHERE student_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $student_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function getAvailableCourses()
{
    $conn = connectDB();
    $sql = "SELECT course_code, course_name FROM courses"; // ใช้ 'code' แทน 'ccode'
    $result = mysqli_query($conn, $sql);
    $courses = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    mysqli_free_result($result);
    return $courses;
}

function getCurrentSchedule($student_code)
{
    $conn = connectDB();
    $sql = "
        SELECT c.DAY AS day, c.TIME AS time, c.course_code, c.course_name
        FROM enrollments e
        JOIN courses c ON e.course_code = c.course_code
        WHERE e.student_code = ?
    ";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $student_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $schedule = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $schedule[] = $row;
        }
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $schedule;
    } else {
        return array();
    }
}

// ฟังก์ชันสำหรับดึงข้อมูลรายวิชาทั้งหมด
// เหมาะสำหรับใช้ในหน้าจัดการรายวิชา
function getCourseDetails()
{
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควร log error นี้
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลรายวิชาทั้งหมดจากตาราง 'courses'
    // ดึงทุกคอลัมน์ที่จำเป็นสำหรับหน้าจัดการรายวิชา
    // คุณจะต้องปรับชื่อตาราง ('courses') และชื่อคอลัมน์ให้ตรงกับฐานข้อมูลของคุณ
    $sql = "SELECT id, course_code, course_name, credits, semester, status, total_seats, available_seats, DAY, TIME, description
            FROM courses
            ORDER BY semester DESC, course_code ASC"; // เรียงตามภาคการศึกษา (ล่าสุดก่อน) และรหัสวิชา

    $result = mysqli_query($conn, $sql); // รันคำสั่ง SQL

    $courses = []; // อาเรย์สำหรับเก็บข้อมูลรายวิชา

    if ($result) {
        // ดึงข้อมูลแต่ละแถวมาเก็บในอาเรย์
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }
        mysqli_free_result($result); // คืนหน่วยความจำของผลลัพธ์
        // ไม่ปิดการเชื่อมต่อที่นี่ เพราะฟังก์ชันอื่นอาจจะใช้ต่อ
        return $courses; // คืนค่าอาเรย์ข้อมูลรายวิชา
    } else {
        // กรณีเกิดข้อผิดพลาดในการรันคำสั่ง SQL
        // ในระบบจริง ควร log error นี้
        // error_log("Error fetching courses: " . mysqli_error($conn)); // แสดง error สำหรับ debugging
        // echo "Error fetching courses: " . mysqli_error($conn); // สามารถ uncomment เพื่อ debugging ได้
        return false; // คืนค่า false หากเกิดข้อผิดพลาด
    }
    // ไม่ปิดการเชื่อมต่อที่นี่
}

// ฟังก์ชันสำหรับค้นหารายวิชาตามคำค้นหา
// เหมาะสำหรับใช้ในหน้าจัดการรายวิชาเมื่อมีคำค้นหา
function searchCourses($search_term)
{ // รับค่า search_term เป็น parameter ที่ต้องมี
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควร log error นี้
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เริ่มต้นเพื่อค้นหารายวิชา
    $sql = "SELECT id, course_code, course_name, credits, semester, status, total_seats, available_seats, DAY, TIME, description
            FROM courses";
    $where_clauses = []; // สำหรับเก็บเงื่อนไข WHERE
    $bind_types = ""; // สำหรับเก็บประเภทข้อมูลของ bind_param
    $bind_params = []; // สำหรับเก็บค่าที่จะ bind

    // ถ้ามีคำค้นหา (ฟังก์ชันนี้จะถูกเรียกเมื่อมีคำค้นหาเท่านั้น)
    if ($search_term !== '') {
        // ใช้ LIKE เพื่อค้นหาในคอลัมน์ course_code และ course_name
        // ปรับคอลัมน์ที่ค้นหาตามความเหมาะสม
        $where_clauses[] = "course_code LIKE ?";
        $where_clauses[] = "course_name LIKE ?";
        $like_search_term = '%' . $search_term . '%'; // เพิ่ม wildcard % เพื่อค้นหาคำที่ตรงกันบางส่วน
        $bind_types .= "ss"; // สองตัวแปรเป็น string
        $bind_params[] = $like_search_term;
        $bind_params[] = $like_search_term;

        // ถ้าต้องการค้นหาในคอลัมน์อื่นๆ ด้วย ให้เพิ่มเงื่อนไขและ bind_param ที่นี่
        // เช่น:
        // $where_clauses[] = "semester LIKE ?";
        // $bind_types .= "s";
        // $bind_params[] = $like_search_term;
    } else {
        // ถ้าไม่มีคำค้นหา (ไม่ควรเกิดขึ้นถ้าเรียกใช้ฟังก์ชันนี้ถูกที่)
        // คืนค่าอาเรย์ว่าง
        return [];
    }


    // เพิ่มเงื่อนไข WHERE เข้าไปใน SQL query
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" OR ", $where_clauses); // ใช้ OR ในการค้นหาหลายคอลัมน์
    } else {
        // ถ้าไม่มีเงื่อนไข WHERE (กรณี search_term ว่าง)
        // อาจจะคืนค่าว่าง หรือดึงทั้งหมดก็ได้ แต่ตาม logic ฟังก์ชันนี้ควรถูกเรียกเมื่อมีคำค้นหาเท่านั้น
        return [];
    }


    // เพิ่ม ORDER BY
    $sql .= " ORDER BY semester DESC, course_code ASC"; // เรียงตามภาคการศึกษา (ล่าสุดก่อน) และรหัสวิชา


    // --- DEBUGGING START ---
    // echo "DEBUG: SQL Query (Search): " . $sql . "<br>";
    // echo "DEBUG: Bind Params (Search): " . implode(", ", $bind_params) . "<br>";
    // echo "DEBUG: Bind Types (Search): " . $bind_types . "<br>";
    // --- DEBUGGING END ---


    $courses = []; // อาเรย์ว่างสำหรับเก็บข้อมูลรายวิชา
    $stmt = null; // กำหนดค่าเริ่มต้นให้ $stmt

    // ใช้ Prepared Statement เพื่อความปลอดภัย
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // ถ้ามี parameter ที่ต้อง bind
        if (!empty($bind_params)) {
            // ใช้ mysqli_stmt_bind_param เพื่อ bind parameter
            // ต้องส่งประเภทข้อมูลและค่าต่างๆ
            mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_params);
        }

        // ประมวลผล Statement
        if (mysqli_stmt_execute($stmt)) {
            // รับผลลัพธ์
            $result = mysqli_stmt_get_result($stmt);

            // ตรวจสอบว่าการรับผลลัพธ์สำเร็จหรือไม่
            if ($result) {
                // ดึงข้อมูลแต่ละแถวที่ได้จากผลลัพธ์มาเก็บในอาเรย์
                while ($row = mysqli_fetch_assoc($result)) {
                    $courses[] = $row;
                }
                mysqli_free_result($result); // คืนหน่วยความจำของผลลัพธ์
            } else {
                // กรณีเกิดข้อผิดพลาดในการรับผลลัพธ์
                // error_log("Error getting result in searchCourses: " . mysqli_error($conn));
                // echo "DEBUG: Error getting result (Search): " . mysqli_error($conn) . "<br>"; // Debugging
                // คืนค่า false หากเกิดข้อผิดพลาด
                mysqli_stmt_close($stmt); // ปิด statement ก่อนคืนค่า
                return false;
            }
        } else {
            // กรณีเกิดข้อผิดพลาดในการรัน Statement
            // error_log("Error executing statement in searchCourses: " . mysqli_stmt_error($stmt));
            // echo "DEBUG: Error executing statement (Search): " . mysqli_stmt_error($stmt) . "<br>"; // Debugging
            // คืนค่า false หากเกิดข้อผิดพลาด
            mysqli_stmt_close($stmt); // ปิด statement ก่อนคืนค่า
            return false;
        }

        // ปิด Statement
        mysqli_stmt_close($stmt);

        // คืนค่าอาเรย์ข้อมูลรายวิชาที่ค้นหาได้
        return $courses;

    } else {
        // กรณีเกิดข้อผิดพลาดในการเตรียม Statement
        // error_log("Error preparing statement in searchCourses: " . mysqli_error($conn));
        // echo "DEBUG: Error preparing statement (Search): " . mysqli_error($conn) . "<br>"; // Debugging
        // คืนค่า false หากเกิดข้อผิดพลาด
        return false;
    }

    // ไม่ปิดการเชื่อมต่อที่นี่
}


// ฟังก์ชันสำหรับดึงข้อมูลรายวิชาเฉพาะตาม ID
// เหมาะสำหรับใช้ในหน้าแก้ไขรายวิชา
function getCourseDetailsById($course_id)
{
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควร log error นี้
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลรายวิชาเฉพาะตาม id
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $sql = "SELECT id, course_code, course_name, credits, semester, status, total_seats, available_seats, DAY, TIME, description
            FROM courses
            WHERE id = ? LIMIT 1"; // ดึงแค่ 1 แถว

    // เตรียม Statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // ผูกค่า ID เข้ากับ Statement
        mysqli_stmt_bind_param($stmt, "i", $course_id); // 'i' สำหรับ Integer ID

        // ประมวลผล Statement
        mysqli_stmt_execute($stmt);

        // รับผลลัพธ์
        $result = mysqli_stmt_get_result($stmt);

        // ดึงข้อมูลแถวเดียวที่พบ
        $course_data = mysqli_fetch_assoc($result);

        // ปิด Statement
        mysqli_stmt_close($stmt);

        // ไม่ปิดการเชื่อมต่อที่นี่

        // คืนค่าข้อมูลรายวิชา (เป็น associative array) หรือ null ถ้าไม่พบ
        return $course_data;

    } else {
        // กรณีเกิดข้อผิดพลาดในการเตรียม Statement
        // ในระบบจริง ควร log error นี้
        // error_log("Error preparing statement in getCourseDetailsById: " . mysqli_error($conn));
        // echo "Error preparing statement in getCourseDetailsById: " . mysqli_error($conn); // Debugging
        // คืนค่า false หากเกิดข้อผิดพลาด
        return false;
    }
}
function getCurrentSemesterAndYear($conn)
{
    // ... โค้ดเดิมของฟังก์ชันนี้ ที่รับ $conn และใช้ $conn ในการคิวรี่ตาราง semester ...
    // ต้องแน่ใจว่าฟังก์ชันนี้ทำงานถูกต้องและคืนค่า ['semester', 'academic_year'] หรือ null/false
    $current_date = date('Y-m-d');
    $query = "SELECT semester, academic_year FROM semester WHERE start_date <= ? AND end_date >= ? LIMIT 1";
    if ($stmt = $conn->prepare($query)) { // <-- ใช้ $conn ที่รับเข้ามา
        $stmt->bind_param("ss", $current_date, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($semester_info = $result->fetch_assoc()) {
            $stmt->close();
            return $semester_info;
        } else {
            $stmt->close();
            error_log("No active semester found: " . $current_date);
            return null;
        }
    } else {
        error_log("DB Error (Prepare Get Current Semester): " . $conn->error);
        return false;
    }
}


// ฟังก์ชันสำหรับดึงรายวิชาที่เปิดให้ลงทะเบียน (เฉพาะข้อมูลระดับวิชา)
// คิวรี่จากตาราง courses โดยตรง
// *** แก้ไข Query ให้เลือก credits และ description ด้วย (สมมติว่าอยู่ในตาราง courses) ***
// *** รับ Parameter $conn ***
function getAvailableCoursesForRegistration($conn)
{
    // ดึงคอลัมน์ที่ต้องการแสดงจากตาราง courses
    // Assumption: ตาราง 'courses' มี course_code (PK), course_name, credits, description
    $query = "SELECT 
                   id,
                   course_name,
                   course_code,
                   credits,
                   semester,
                   status,
                   total_seats,
                   available_seats,
                   DAY,
                   TIME,
                   description,
                   course_id 
                FROM 
                   courses 
                ORDER BY 
                   course_code";

    // *** ใช้ $conn ที่รับเข้ามา ***
    if ($result = $conn->query($query)) { // ใช้ query() เนื่องจากไม่มี parameter ที่ต้อง bind
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $result->free(); // คืนหน่วยความจำ
        return $courses; // คืนค่าเป็น Array ของวิชาทั้งหมด
    } else {
        error_log("DB Error (Get Available Courses): " . $conn->error);
        return false; // ระบุว่าเกิดข้อผิดพลาดฐานข้อมูล
    }
}


// ฟังก์ชันสำหรับดึงรายวิชาที่นิสิตลงทะเบียนแล้วในภาคการศึกษาและปีการศึกษาที่ระบุ
// คิวรี่จากตาราง enrollments และ join กับ courses
// *** แก้ไข Query ให้เลือก credits และ description จากตาราง courses ***
// *** รับ Parameter $conn ***
function getRegisteredCourses($student_code, $semester, $academic_year, $conn)
{
    // คิวรี่ตาราง enrollments และเชื่อมกับ courses เพื่อดึงรายละเอียดวิชาที่ลงทะเบียนแล้ว
    // กรองตาม student_code, semester, และ academic_year
    $query = "SELECT
                  e.enrollment_id, -- อาจต้องใช้ enrollment_id สำหรับการยกเลิก
                  e.student_code,
                  e.course_code,
                  c.course_name,
                  c.credits,       -- Select credits จาก Courses
                  c.description    -- Select description จาก Courses
              FROM
                  enrollments e
              JOIN
                  courses c ON e.course_code = c.course_code -- เชื่อม enrollments กับ courses
              WHERE
                  e.student_code = ?
                  AND e.semester = ? -- กรองตาม semester
                  AND e.academic_year = ? -- กรองตาม academic_year
              ORDER BY
                  e.course_code ASC";

    // *** ใช้ $conn ที่รับเข้ามา ***
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters: student_code(s), semester(i), academic_year(i)
        $stmt->bind_param("sss", $student_code, $semester, $academic_year);
        $stmt->execute();
        $result = $stmt->get_result();

        $registered_courses = [];
        while ($row = $result->fetch_assoc()) {
            $registered_courses[] = $row;
        }

        $stmt->close();
        return $registered_courses;

    } else {
        error_log("DB Error (Get Registered Courses): " . $conn->error);
        return false; // ระบุว่าเกิดข้อผิดพลาดฐานข้อมูล
    }
}

// ฟังก์ชันสำหรับลงทะเบียนรายวิชา (INSERT into enrollments)
// *** ควรรับ Parameter $conn ***
// *** ต้องบันทึก semester และ academic_year (ซึ่งเราได้แก้ไขไปแล้ว) ***
function registerCourse($student_code, $course_code, $semester, $academic_year, $conn)
{
    // ... โค้ดเดิมของฟังก์ชัน registerCourse ที่รับ $conn, semester, year และใช้ $conn ...
    // ตรวจสอบว่านิสิตลงทะเบียนวิชานี้ในภาค/ปีนี้แล้วหรือยัง
    $query_check = "SELECT enrollment_id FROM enrollments WHERE student_code = ? AND course_code = ? AND semester = ? AND academic_year = ?";
    if ($stmt_check = $conn->prepare($query_check)) { // ใช้ $conn ที่รับเข้ามา
        $stmt_check->bind_param("ssss", $student_code, $course_code, $semester, $academic_year);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            return "คุณได้ลงทะเบียนวิชานี้ในภาคการศึกษานี้แล้ว";
        }
        $stmt_check->close();
    } else {
        error_log("DB Error (Prepare Check Enrollment): " . $conn->error);
        return "เกิดข้อผิดพลาดในการตรวจสอบการลงทะเบียน: " . $conn->error;
    }

    // Insert
    $query_insert = "INSERT INTO enrollments (student_code, course_code, semester, academic_year, enrollment_date) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
    if ($stmt_insert = $conn->prepare($query_insert)) { // ใช้ $conn ที่รับเข้ามา
        $stmt_insert->bind_param("ssii", $student_code, $course_code, $semester, $academic_year);
        if ($stmt_insert->execute()) {
            $stmt_insert->close();
            return true;
        } else {
            $stmt_insert->close();
            error_log("DB Error (Execute Register Course): " . $conn->error);
            return "เกิดข้อผิดพลาดในการบันทึกการลงทะเบียน: " . $conn->error;
        }
    } else {
        error_log("DB Error (Prepare Register Course): " . $conn->error);
        return "เกิดข้อผิดพลาดในการเตรียมคำสั่งลงทะเบียน: " . $conn->error;
    }
}

// ฟังก์ชันสำหรับยกเลิกการลงทะเบียน (DELETE from enrollments)
// *** ควรรับ Parameter $conn ***
// *** ต้องกรองด้วย semester และ academic_year (ซึ่งเราได้แก้ไขไปแล้ว) ***
function unregisterCourse($student_code, $course_code, $semester, $academic_year, $conn)
{
    // ... โค้ดเดิมของฟังก์ชัน unregisterCourse ที่รับ $conn, semester, year และใช้ $conn ...
    // หากตาราง enrollments ใช้ course_code, semester, year เป็น Unique key ร่วมกัน
    $query_delete = "DELETE FROM enrollments WHERE student_code = ? AND course_code = ? AND semester = ? AND academic_year = ?";
    if ($stmt_delete = $conn->prepare($query_delete)) { // ใช้ $conn ที่รับเข้ามา
        $stmt_delete->bind_param("ssss", $student_code, $course_code, $semester, $academic_year);
        if ($stmt_delete->execute()) {
            if ($stmt_delete->affected_rows > 0) {
                $stmt_delete->close();
                return true;
            } else {
                $stmt_delete->close();
                return "ไม่พบรายการลงทะเบียนที่ต้องการยกเลิก";
            }
        } else {
            $stmt_delete->close();
            error_log("DB Error (Execute Unregister Course): " . $conn->error);
            return "เกิดข้อผิดพลาดในการยกเลิกการลงทะเบียน: " . $conn->error;
        }
    } else {
        error_log("DB Error (Prepare Unregister Course): " . $conn->error);
        return "เกิดข้อผิดพลาดในการเตรียมคำสั่งยกเลิกการลงทะเบียน: " . $conn->error;
    }
}


// ฟังก์ชัน Helper เพื่อดึง Error ล่าสุดจากฐานข้อมูล
// *** ควรรับ Parameter $conn ***
function getLastDBError($conn)
{
    // *** ใช้ $conn ที่รับเข้ามา ***
    return $conn->error;
}

function getStudentSchedule($student_code)
{
    global $conn; // Assuming $conn is your database connection

    $sql = "SELECT 
                c.course_code,
                c.course_name,
                c.DAY AS day,
                c.TIME AS time
            FROM 
                enrollments e
            JOIN 
                courses c ON e.course_code = c.course_code
            WHERE 
                e.student_code = '$student_code'";

    $result = mysqli_query($conn, $sql);
    $schedule = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $schedule[] = $row;
        }
    }
    return $schedule;
}

function getStudentData($student_code)
{
    $conn = connectDB(); // Ensure the database connection is established
    $sql = "SELECT
                    s.student_code,
                    s.first_name,
                    s.last_name,
                    si.email,
                    si.phone_number,
                    f.faculty_name,  -- Added f.faculty_name
                    m.major_name,
                    si.year,
                    si.date_of_birth,
                    si.address,
                    si.profile_picture
                FROM
                    students s
                LEFT JOIN
                    student_info si ON s.student_code = si.student_code
                LEFT JOIN
                    faculties f ON si.faculty_id = f.faculty_id  -- Corrected join condition to use si.faculty_id
                LEFT JOIN
                    majors m ON si.major_id = m.major_id      -- Corrected join condition to use si.major_id
                WHERE
                    s.student_code = '$student_code'";

    $result = mysqli_query($conn, $sql);
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}
// ฟังก์ชันสำหรับดึงข้อมูลคณะ โดยอ้างอิงจากรหัสนิสิต
function getFaculties($student_code)
{
    global $conn;
    $sql = "SELECT f.faculty_id, f.faculty_name
            FROM faculties f
            INNER JOIN student_info si ON f.faculty_id = si.faculty_id
            WHERE si.student_code = '$student_code'";
    $result = mysqli_query($conn, $sql);
    $faculties = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $faculties[] = $row;
        }
    }
    return $faculties;
}

// ฟังก์ชันสำหรับดึงข้อมูลสาขา โดยอ้างอิงจากรหัสนิสิต
function getMajors($student_code)
{
    global $conn;
    $sql = "SELECT m.major_id, m.major_name
            FROM majors m
            INNER JOIN student_info si ON m.major_id = si.major_id
            WHERE si.student_code = '$student_code'";
    $result = mysqli_query($conn, $sql);
    $majors = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $majors[] = $row;
        }
    }
    return $majors;
}
function updateStudentInfo($student_code, $email, $phone_number, $faculty_id, $major_id, $year, $date_of_birth, $address)
{
    global $conn; // ใช้ connection ที่สร้างไว้แล้ว
    $sql = "UPDATE student_info SET email = ?, phone_number = ?, faculty_id = ?, major_id = ?, year = ?, date_of_birth = ?, address = ? WHERE student_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiiisss", $email, $phone_number, $faculty_id, $major_id, $year, $date_of_birth, $address, $student_code);
    return mysqli_stmt_execute($stmt);
}
function getStudentProfilePicture($student_code)
{
    global $conn; // ใช้ connection ที่สร้างไว้แล้ว
    $sql = "SELECT profile_picture FROM student_info WHERE student_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $student_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['profile_picture'];
    } else {
        return null;
    }
}
function handleFileUpload($input_name)
{
    // Check if a file was uploaded and there were no errors
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
        $file = $_FILES[$input_name];
        $file_name = basename($file['name']); // Get the original file name
        $file_size = $file['size'];
        $file_tmp_name = $file['tmp_name'];
        // Removed unused variable $file_type

        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Define allowed file types (add more as needed)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        // Define max file size (in bytes)
        $max_file_size = 5 * 1024 * 1024; // 5MB

        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            return "Error: Invalid file type. Allowed types are " . implode(', ', $allowed_extensions);
        }

        // Validate file size
        if ($file_size > $max_file_size) {
            return "Error: File size exceeds the maximum limit of " . ($max_file_size / (1024 * 1024)) . "MB.";
        }

        // Determine the subfolder based on the input name
        $subfolder = '';
        switch ($input_name) {
            case 'id_card':
                $subfolder = 'id_card/';
                break;
            case 'student_image':
                $subfolder = 'student_image/';
                break;
            case 'change_name':
                $subfolder = 'change_name/';
                break;
            case 'parent_guarantee':
                $subfolder = 'parent_guarantee/';
                break;
            case 'consent_agreement':
                $subfolder = 'consent_agreement/';
                break;
            default:
                $subfolder = 'general/'; // Default folder
        }

        // Create the subfolder if it doesn't exist
        $upload_dir = "../images/{$subfolder}";
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return "Error: Failed to create subfolder.";
            }
        }

        // Create a unique file name (using student code and original extension)
        global $student_code; // Assuming $student_code is available in this scope
        $new_file_name = "{$student_code}.{$file_ext}";
        $destination_path = ".."+"..{$upload_dir}{$new_file_name}";  // Set the desired path

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($file_tmp_name, $destination_path)) {
            return $destination_path; // Return the file path
        } else {
            return "Error: Failed to upload file.";
        }
    } elseif (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] != 4) {
        // Handle other errors apart from no file being uploaded
        return "Error: " . $_FILES[$input_name]['error'];
    } else {
        // Handle cases where no file is sent
        return null; // Return null or an appropriate value based on your logic
    }
}
function storeStudentDocuments(
    $student_code,
    $id_card_path,
    $student_image_path,
    $change_name_path,
    $parent_guarantee_path,
    $consent_agreement_path
) {
    global $conn; // Ensure you have a valid database connection

    $sql = "INSERT INTO students_documents (
        student_code,
        id_card_path,
        student_image_path,
        change_name_path,
        parent_guarantee_path,
        consent_agreement_path
    ) VALUES (
        '$student_code',
        '$id_card_path',
        '$student_image_path',
        '$change_name_path',
        '$parent_guarantee_path',
        '$consent_agreement_path'
    )";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}
// ฟังก์ชันสำหรับดึงข้อมูลประกาศทั้งหมด
function getAnnouncements()
{
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควรบันทึกข้อผิดพลาดลงใน log แทนการคืนค่า false เฉยๆ
        // error_log("Database connection failed in getAnnouncements: " . mysqli_connect_error());
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลประกาศทั้งหมดจากตาราง 'news'
    // ดึงคอลัมน์ id, title, details, created_at (ใช้เป็นผู้ประกาศ), media, date
    // *** อิงตามโครงสร้างตาราง news ที่มี created_at เป็น VARCHAR(10) สำหรับเก็บชื่อผู้ประกาศ ***
    $sql = "SELECT
                id,
                title,
                details,
                created_at, -- ใช้คอลัมน์ created_at สำหรับผู้ประกาศ
                media,
                date
            FROM
                news
            ORDER BY
                date DESC, id DESC"; // เรียงตาม date (ล่าสุดก่อน) และ id (ปรับการเรียงตามความเหมาะสม)

    $result = mysqli_query($conn, $sql); // รันคำสั่ง SQL

    $news_items = []; // อาเรย์สำหรับเก็บข้อมูลข่าว/ประกาศ

    if ($result) {
        // ดึงข้อมูลแต่ละแถวมาเก็บในอาเรย์
        while ($row = mysqli_fetch_assoc($result)) {
            $news_items[] = $row;
        }
        mysqli_free_result($result); // คืนหน่วยความจำของผลลัพธ์
        // ไม่ปิดการเชื่อมต่อที่นี่ เพราะฟังก์ชันอื่นอาจจะใช้ต่อ
        return $news_items; // คืนค่าอาเรย์ข้อมูลข่าว/ประกาศ
    } else {
        // กรณีเกิดข้อผิดพลาดในการรันคำสั่ง SQL
        // ในระบบจริง ควร log error นี้
        // error_log("Error fetching news items: " . mysqli_error($conn)); // แสดง error สำหรับ debugging
        // echo "DEBUG: Error fetching news items: " . mysqli_error($conn) . "<br>"; // สามารถ uncomment เพื่อ debugging ได้
        return false; // คืนค่า false หากเกิดข้อผิดพลาด
    }
    // ไม่ปิดการเชื่อมต่อที่นี่
}

// ฟังก์ชันสำหรับดึงข้อมูลประกาศเฉพาะตาม ID จากตาราง 'news'
// พร้อมชื่อผู้ประกาศ (ที่เก็บใน created_at)
function getAnnouncementById($announcement_id)
{
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควร log error นี้
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลประกาศเฉพาะตาม id จากตาราง 'news'
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    // ดึงคอลัมน์ id, title, details, created_at (ใช้เป็นผู้ประกาศ), media, date
    // *** อิงตามโครงสร้างตาราง news ที่มี created_at เป็น VARCHAR(10) สำหรับเก็บชื่อผู้ประกาศ ***
    $sql = "SELECT
                id,
                title,
                details,
                created_at, -- ใช้คอลัมน์ created_at สำหรับผู้ประกาศ
                media,
                date
            FROM
                news
            WHERE
                id = ? LIMIT 1"; // ดึงแค่ 1 แถว

    // เตรียม Statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // ผูกค่า ID เข้ากับ Statement
        mysqli_stmt_bind_param($stmt, "i", $announcement_id); // 'i' สำหรับ Integer ID

        // ประมวลผล Statement
        mysqli_stmt_execute($stmt);

        // รับผลลัพธ์
        $result = mysqli_stmt_get_result($stmt);

        // ดึงข้อมูลแถวเดียวที่พบ
        $announcement_data = mysqli_fetch_assoc($result);

        // ปิด Statement
        mysqli_stmt_close($stmt);

        // ไม่ปิดการเชื่อมต่อที่นี่

        // คืนค่าข้อมูลประกาศ (เป็น associative array) หรือ null ถ้าไม่พบ
        return $announcement_data;

    } else {
        // กรณีเกิดข้อผิดพลาดในการเตรียม Statement
        // ในระบบจริง ควร log error นี้
        // error_log("Error preparing statement in getAnnouncementById: " . mysqli_error($conn));
        // echo "DEBUG: Error preparing statement in getAnnouncementById: " . mysqli_error($conn) . "<br>"; // Debugging
        // คืนค่า false หากเกิดข้อผิดพลาด
        return false;
    }
}
// ฟังก์ชันสำหรับดึงข้อมูลนักศึกษาทั้งหมดจากตาราง 'students'
// เหมาะสำหรับใช้ในหน้าจัดการผู้ใช้
// ไม่ดึงข้อมูลรหัสผ่านเพื่อความปลอดภัย
function getStudentDetails()
{
    // เรียกใช้ฟังก์ชันเชื่อมต่อฐานข้อมูลที่มีอยู่ในไฟล์เดียวกัน
    // ตรวจสอบให้แน่ใจว่าฟังก์ชัน connectDB() ทำงานได้อย่างถูกต้องและคืนค่าการเชื่อมต่อ
    $conn = connectDB();

    // ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
    if (!$conn) {
        // ในระบบจริง ควรบันทึกข้อผิดพลาดลงใน log แทนการคืนค่า false เฉยๆ
        // error_log("Database connection failed in getStudentDetails: " . mysqli_connect_error());
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลนักศึกษาทั้งหมดจากตาราง 'students'
    // ดึงคอลัมน์ id, student_code, first_name, last_name, thaiid
    // *** ไม่ดึงคอลัมน์ password เพื่อความปลอดภัย ***
    // คุณจะต้องปรับชื่อตาราง ('students') และชื่อคอลัมน์ให้ตรงกับฐานข้อมูลของคุณ
    $sql = "SELECT id, student_code, first_name, last_name, thaiid
            FROM students
            ORDER BY student_code ASC"; // เรียงตามรหัสนักศึกษา

    $result = mysqli_query($conn, $sql); // รันคำสั่ง SQL

    $students = []; // อาเรย์สำหรับเก็บข้อมูลนักศึกษา

    if ($result) {
        // ดึงข้อมูลแต่ละแถวมาเก็บในอาเรย์
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        mysqli_free_result($result); // คืนหน่วยความจำของผลลัพธ์
        // ไม่ปิดการเชื่อมต่อที่นี่ เพราะฟังก์ชันอื่นอาจจะใช้ต่อ
        return $students; // คืนค่าอาเรย์ข้อมูลนักศึกษา
    } else {
        // กรณีเกิดข้อผิดพลาดในการรันคำสั่ง SQL
        // ในระบบจริง ควร log error นี้
        // error_log("Error fetching students: " . mysqli_error($conn)); // แสดง error สำหรับ debugging
        // echo "DEBUG: Error fetching students: " . mysqli_error($conn) . "<br>"; // สามารถ uncomment เพื่อ debugging ได้
        return false; // คืนค่า false หากเกิดข้อผิดพลาด
    }
    // ไม่ปิดการเชื่อมต่อที่นี่
}
function getStudentById($student_id)
{
    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        // ในระบบจริง ควร log error นี้
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลนักศึกษาเฉพาะตาม id จากตาราง 'students'
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    // ดึงคอลัมน์ id, student_code, first_name, last_name, thaiid
    // *** ไม่ดึงคอลัมน์ password เพื่อความปลอดภัย ***
    $sql = "SELECT id, student_code, first_name, last_name, thaiid
            FROM students
            WHERE id = ? LIMIT 1"; // ดึงแค่ 1 แถว

    // เตรียม Statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // ผูกค่า ID เข้ากับ Statement
        mysqli_stmt_bind_param($stmt, "i", $student_id); // 'i' สำหรับ Integer ID

        // ประมวลผล Statement
        mysqli_stmt_execute($stmt);

        // รับผลลัพธ์
        $result = mysqli_stmt_get_result($stmt);

        // ดึงข้อมูลแถวเดียวที่พบ
        $student_data = mysqli_fetch_assoc($result);

        // ปิด Statement
        mysqli_stmt_close($stmt);

        // ไม่ปิดการเชื่อมต่อที่นี่

        // คืนค่าข้อมูลนักศึกษา (เป็น associative array) หรือ null ถ้าไม่พบ
        return $student_data;

    } else {
        // กรณีเกิดข้อผิดพลาดในการเตรียม Statement
        // ในระบบจริง ควร log error นี้
        // error_log("Error preparing statement in getStudentById: " . mysqli_error($conn));
        // echo "DEBUG: Error preparing statement in getStudentById: " . mysqli_error($conn) . "<br>"; // Debugging
        // คืนค่า false หากเกิดข้อผิดพลาด
        return false;
    }
}
function getSemesters()
{
    // เรียกใช้ฟังก์ชันเชื่อมต่อฐานข้อมูลที่มีอยู่ในไฟล์เดียวกัน
    // ตรวจสอบให้แน่ใจว่าฟังก์ชัน connectDB() ทำงานได้อย่างถูกต้องและคืนค่าการเชื่อมต่อ
    $conn = connectDB();

    // ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
    if (!$conn) {
        // ในระบบจริง ควรบันทึกข้อผิดพลาดลงใน log แทนการคืนค่า false เฉยๆ
        // error_log("Database connection failed in getSemesters: " . mysqli_connect_error());
        return false; // คืนค่า false หากเชื่อมต่อฐานข้อมูลไม่ได้
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลภาคการศึกษาทั้งหมดจากตาราง 'semesters'
    // ดึงคอลัมน์ id, semester_name, start_date, end_date
    // คุณจะต้องปรับชื่อตาราง ('semesters') และชื่อคอลัมน์ให้ตรงกับฐานข้อมูลของคุณ
    $sql = "SELECT id, semester_name, start_date, end_date
            FROM semester
            ORDER BY semester_name DESC"; // เรียงตามชื่อภาคการศึกษา (ล่าสุดก่อน) หรือตาม start_date ก็ได้

    $result = mysqli_query($conn, $sql); // รันคำสั่ง SQL

    $semesters = []; // อาเรย์สำหรับเก็บข้อมูลภาคการศึกษา

    if ($result) {
        // ดึงข้อมูลแต่ละแถวมาเก็บในอาเรย์
        while ($row = mysqli_fetch_assoc($result)) {
            $semesters[] = $row;
        }
        mysqli_free_result($result); // คืนหน่วยความจำของผลลัพธ์
        // ไม่ปิดการเชื่อมต่อที่นี่ เพราะฟังก์ชันอื่นอาจจะใช้ต่อ
        return $semesters; // คืนค่าอาเรย์ข้อมูลภาคการศึกษา
    } else {
        // กรณีเกิดข้อผิดพลาดในการรันคำสั่ง SQL
        // ในระบบจริง ควร log error นี้
        // error_log("Error fetching semesters: " . mysqli_error($conn)); // แสดง error สำหรับ debugging
        // echo "DEBUG: Error fetching semesters: " . mysqli_error($conn) . "<br>"; // สามารถ uncomment เพื่อ debugging ได้
        return false; // คืนค่า false หากเกิดข้อผิดพลาด
    }
    // ไม่ปิดการเชื่อมต่อที่นี่
}
function getAnnouncementsByTeacher($teacherStaffId, $conn)
{
    // คิวรี่ตาราง news โดยกรองตาม author_id
    $query = "SELECT id, title, details, media, date, author_id, created_at FROM news WHERE author_id = ? ORDER BY date DESC, created_at DESC";

    // *** ใช้ $conn ที่รับเข้ามา ***
    if ($stmt = $conn->prepare($query)) { // <-- บรรทัด 905 ตาม Error Stack Trace (conceptually)
        // Bind parameter: teacherStaffId (สมมติว่าเป็น VARCHAR ในตาราง users)
        $stmt->bind_param("s", $teacherStaffId);
        $stmt->execute();
        $result = $stmt->get_result();

        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }

        $stmt->close();
        return $announcements; // คืนค่าเป็น Array ของประกาศ

    } else {
        // Log the specific database error
        error_log("DB Error (Prepare Get Announcements By Teacher): " . $conn->error); // <-- ใช้ $conn ที่รับเข้ามา
        return false; // ระบุว่าเกิดข้อผิดพลาดฐานข้อมูล
    }
}

// *** ฟังก์ชันนี้ต้องรับ Parameter สองตัว: รหัสอาจารย์ ($teacherStaffId) และ Object การเชื่อมต่อฐานข้อมูล ($conn) ***
function getTeacherCourseAssignments($teacherStaffId, $conn)
{
    // *** สำคัญ: ตรวจสอบว่า $conn เป็น Object การเชื่อมต่อฐานข้อมูลที่ถูกต้องหรือไม่ ***
    // การตรวจสอบนี้จะช่วยป้องกัน Error "Call to a member function prepare() on null" หากมีการเรียกใช้ฟังก์ชันนี้โดยไม่ได้ส่ง $conn หรือส่งค่าที่ไม่ใช่การเชื่อมต่อ
    if ($conn === null || !is_object($conn) || !method_exists($conn, 'prepare')) {
        error_log("Invalid database connection object passed to getTeacherCourseAssignments.");
        // คุณอาจจะแจ้งเตือนผู้ใช้ หรือจัดการ error ในรูปแบบอื่นได้
        // ในที่นี้จะคืนค่า false เพื่อให้โค้ดที่เรียกฟังก์ชันนี้ทราบว่าเกิดข้อผิดพลาด
        return false;
    }

    // กำหนดคำสั่ง SQL เพื่อดึงข้อมูล Assignment
    // - ดึงข้อมูลจากตาราง teacher_course_assignments (tca)
    // - เชื่อม (JOIN) กับตาราง courses (c) เพื่อดึงชื่อวิชา (course_name) โดยใช้ course_code
    // - กรอง (WHERE) ข้อมูลเฉพาะ Assignment ที่มี teacher_id ตรงกับ $teacherStaffId ที่รับเข้ามา
    // - จัดเรียง (ORDER BY) ผลลัพธ์ตามปีการศึกษา ภาคการศึกษา และรหัสวิชา
    $query = "SELECT
                  tca.assignment_id,
                  tca.teacher_id,
                  tca.course_code,
                  c.course_name,  -- ดึงชื่อวิชาจากตาราง courses
                  tca.semester,
                  tca.academic_year,
                  tca.created_at -- อาจจะไม่ต้องใช้ในการแสดงผล แต่ดึงมาเผื่อ
              FROM
                  teacher_course_assignments tca
              JOIN
                  courses c ON tca.course_code = c.course_code -- เชื่อมตารางโดยใช้ course_code
              WHERE
                  tca.teacher_id = ? -- กรองตาม teacher_id ที่รับเข้ามา
              ORDER BY
                  tca.academic_year DESC, tca.semester DESC, tca.course_code ASC"; // จัดเรียงจากปี/ภาค ล่าสุดไปเก่าสุด

    // เตรียมคำสั่ง SQL ด้วย prepared statement เพื่อป้องกัน SQL Injection
    // *** ใช้ $conn ที่รับเข้ามาในการเรียก prepare() ***
    if ($stmt = $conn->prepare($query)) {
        // Bind parameter: ผูกค่า $teacherStaffId เข้ากับเครื่องหมาย ? ใน Query
        // "s" หมายถึง $teacherStaffId เป็น string (สมมติว่า teacher_id/staff_id เป็น VARCHAR)
        $stmt->bind_param("s", $teacherStaffId);

        // ประมวลผลคำสั่ง
        $stmt->execute();

        // ดึงผลลัพธ์จากการคิวรี่
        $result = $stmt->get_result();

        $assignments = [];
        // วนลูปเพื่อดึงข้อมูลแต่ละแถวที่ได้จากฐานข้อมูล
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row; // เพิ่มแถวข้อมูลลงใน Array $assignments
        }

        $stmt->close(); // ปิด statement เพื่อคืนทรัพยากร

        return $assignments; // คืนค่าเป็น Array ของ Assignment ที่พบ

    } else {
        // หากเกิดข้อผิดพลาดในการเตรียมคำสั่ง (มักเกิดจาก Query ผิด)
        // บันทึก Error เฉพาะเจาะจงลงใน Server Error Log (มักอยู่ที่ D:\XAMPP\apache\logs\error.log)
        error_log("DB Error (Prepare Get Teacher Course Assignments): " . $conn->error);
        // คืนค่า false เพื่อให้โค้ดที่เรียกฟังก์ชันนี้ทราบว่าเกิดข้อผิดพลาดฐานข้อมูล
        return false;
    }
}
function getAcademicResults($student_code)
{
    global $conn; // ใช้ตัวแปรการเชื่อมต่อฐานข้อมูลที่กำหนดแบบ global

    // ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่
    if (!$conn) {
        error_log("Database connection not available in getAcademicResults.");
        return false;
    }

    // Query เพื่อดึงข้อมูลผลการเรียน
    // เข้าร่วมตาราง enrollments, courses และ grades
    // เลือกข้อมูลปีการศึกษา, ภาคเรียน, รหัสวิชา, ชื่อวิชา, หน่วยกิต (สมมติว่ามีในตาราง courses หรือ enrollments) และเกรด
    $query = "SELECT
                e.academic_year,
                e.semester,
                e.course_code,
                c.course_name,
                c.credits, -- สมมติว่ามีคอลัมน์ credits ในตาราง courses
                g.grade_value
              FROM
                enrollments e
              JOIN
                courses c ON e.course_code = c.course_code
              LEFT JOIN
                grades g ON e.enrollment_id = g.enrollment_id -- ใช้ LEFT JOIN เพื่อรวมวิชาที่ยังไม่มีเกรด (grade_value จะเป็น NULL)
              WHERE
                e.student_code = ?
              ORDER BY
                e.academic_year ASC, e.semester ASC, e.course_code ASC"; // เรียงตามปี ภาคเรียน และรหัสวิชา

    $academic_results = [];

    if ($stmt = $conn->prepare($query)) {
        // Bind parameter: student_code (string - 's')
        $stmt->bind_param("s", $student_code);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch all results into an array
        while ($row = $result->fetch_assoc()) {
            // ถ้า grade_value เป็น NULL แสดงว่ายังไม่มีการบันทึกเกรด อาจแสดงเป็น '-' หรือ 'รอผล'
            $row['grade_value'] = $row['grade_value'] ?? '-';
            $academic_results[] = $row;
        }

        // Close the statement
        $stmt->close();

        return $academic_results;

    } else {
        // เกิดข้อผิดพลาดในการเตรียม query
        error_log("Error preparing statement in getAcademicResults: " . $conn->error);
        return false;
    }
}
function getGradePoint($grade)
{
    // ทำความสะอาดเกรด (ลบช่องว่าง) และแปลงเป็นตัวพิมพ์ใหญ่เพื่อเปรียบเทียบ
    $cleaned_grade = strtoupper(trim($grade));

    switch ($cleaned_grade) {
        case 'A':
        case 'A+': // บางมหาวิทยาลัยอาจมี A+
            return 4.00;
        case 'B+':
            return 3.50;
        case 'B':
            return 3.00;
        case 'C+':
            return 2.50;
        case 'C':
            return 2.00;
        case 'D+':
            return 1.50;
        case 'D':
            return 1.00;
        case 'F':
            return 0.00;
        // เกรดอื่นๆ ที่ไม่ใช้คำนวณ GPAX
        case 'W':   // ถอนรายวิชา
        case 'I':   // ไม่สมบูรณ์
        case 'S':   // ผ่าน (Satisfactory)
        case 'U':   // ไม่ผ่าน (Unsatisfactory)
        case 'AU':  // Audit
        case '-':   // อาจใช้แทนยังไม่บันทึกเกรด
            return null; // ส่งกลับ null เพื่อระบุว่าเกรดนี้ไม่มีแต้มที่ใช้คำนวณ GPA/GPAX
        default:
            // กรณีเกรดไม่ถูกต้อง หรือเป็นค่าอื่นที่ไม่ได้คาดหมาย
            error_log("Unknown grade encountered: " . $grade); // บันทึก error
            return null;
    }
}
function getAllFaculties()
{
    global $conn; // Access the global database connection variable

    // Prepare the SQL query
    $sql = "SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name";

    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all results as an associative array
        $faculties = $result->fetch_all(MYSQLI_ASSOC);

        // Free result set
        $result->free();

        return $faculties;
    } else {
        // Handle query error (you might want to log this error in a real application)
        error_log("Error fetching faculties: " . $conn->error);
        return []; // Return an empty array on failure
    }
}

/**
 * Gets all majors from the database.
 *
 * @global mysqli $conn The database connection object.
 * @return array An array of associative arrays representing majors, or an empty array on failure.
 */
function getAllMajors()
{
    global $conn; // Access the global database connection variable

    // Prepare the SQL query
    $sql = "SELECT * FROM majors";

    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all results as an associative array
        $majors = $result->fetch_all(MYSQLI_ASSOC);

        // Free result set
        $result->free();

        return $majors;
    } else {
        // Handle query error (you might want to log this error in a real application)
        error_log("Error fetching majors: " . $conn->error);
        return []; // Return an empty array on failure
    }
}
?>