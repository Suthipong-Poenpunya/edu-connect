<?php
// teacher_record_grades.php - เนื้อหาสำหรับบันทึกผลการเรียน.
// ไฟล์นี้จะถูก include โดย teacher_dashboard.php.
// จึงไม่มี Session Start, Include db.php, Auth Check, POST Handling หลัก, Header Calls, หรือ HTML Structure หลัก.

// ตรวจสอบว่าตัวแปร $conn และ $loggedInStaffId พร้อมใช้งานหรือไม่ (ควรมาจาก teacher_dashboard.php)
if (!isset($conn) || !isset($loggedInStaffId)) {
    echo '<div class="alert alert-danger">ข้อผิดพลาดในการเข้าถึงส่วนบันทึกผลการเรียน: ข้อมูลระบบไม่สมบูรณ์</div>';
    exit(); // หยุดการทำงานของไฟล์ที่ถูก include นี้
}

// --- PHP: ดึงข้อมูลสำหรับแสดงผล ---

// 1. Fetch the list of courses taught by the logged-in teacher for the selection dropdown
$teacher_assignments = []; // Array to hold the teacher's course assignments
// Query teacher_course_assignments and join with courses
// Use course_code for joining with 'courses' table based on user's schema
$query_assignments = "SELECT
                         tca.assignment_id,
                         tca.course_code, -- Get course_code from assignment table
                         c.course_name,    -- Get course_name from courses table
                         tca.semester,
                         tca.academic_year
                      FROM
                         teacher_course_assignments tca
                      JOIN
                         courses c ON tca.course_code = c.course_code -- Join on course_code
                      WHERE
                         tca.teacher_id = ? -- Filter by the logged-in teacher's ID (VARCHAR or INT based on users table)
                      ORDER BY
                         tca.academic_year DESC, tca.semester DESC, tca.course_code ASC";

if ($stmt_assignments = $conn->prepare($query_assignments)) {
    // Bind teacher_id (Assuming teacher_id in teacher_course_assignments links to users.staff_id, which is VARCHAR. Adjust 's' if it's INT)
    $stmt_assignments->bind_param("s", $loggedInStaffId);
    $stmt_assignments->execute();
    $result_assignments = $stmt_assignments->get_result();

    // Fetch all results into an array
    while ($row_assignment = $result_assignments->fetch_assoc()) {
        $teacher_assignments[] = $row_assignment;
    }

    $stmt_assignments->close(); // Close the statement

} else {
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงรายการรายวิชาที่คุณสอน: ' . $conn->error . '</div>';
}


// Get the selected course details from GET parameters (if a selection form was submitted)
// These parameters will be used in the next step to fetch enrolled students
$selected_assignment_id = $_GET['assignment_id'] ?? null;
$selected_course_code = null;
$selected_semester = null;
$selected_academic_year = null;
$selected_course_name = null; // To display in the student list header

// If an assignment was selected, fetch its details to use for querying enrollments
if ($selected_assignment_id !== null) {
    // Fetch the specific assignment details to ensure it belongs to the teacher
    $query_selected = "SELECT tca.course_code, tca.semester, tca.academic_year, c.course_name
                       FROM teacher_course_assignments tca
                       JOIN courses c ON tca.course_code = c.course_code
                       WHERE tca.assignment_id = ? AND tca.teacher_id = ?";
    if ($stmt_selected = $conn->prepare($query_selected)) {
        // Bind parameters: assignment_id (INT), teacher_id (VARCHAR or INT)
        $stmt_selected->bind_param("is", $selected_assignment_id, $loggedInStaffId); // Adjust 's' if teacher_id is INT
        $stmt_selected->execute();
        $result_selected = $stmt_selected->get_result();
        if ($row_selected = $result_selected->fetch_assoc()) {
            $selected_course_code = $row_selected['course_code'];
            $selected_semester = $row_selected['semester'];
            $selected_academic_year = $row_selected['academic_year'];
            $selected_course_name = $row_selected['course_name']; // Store course name
        }
        $stmt_selected->close();
    } else {
         echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงรายละเอียดรายวิชาที่เลือก: ' . $conn->error . '</div>';
    }
}


// 3. Fetch Enrolled Students and their Grades (Conditional - only if a course is selected)
$enrolled_students = []; // Array to hold the list of enrolled students and their grades

// Only attempt to fetch students if a valid course assignment was selected
if ($selected_course_code !== null && $selected_semester !== null && $selected_academic_year !== null) {

    // Query the 'enrollments' table for students in the selected course/semester/year
    // Join with 'students' table to get student name (using student_code as join key as per user's table)
    // Left Join with 'grades' table to get the existing grade value (if any) using enrollment_id
    $query_students = "SELECT
                          e.enrollment_id, -- Need enrollment_id to link grades
                          e.student_code, -- Get student_code from enrollments
                          s.first_name,   -- Get first_name from students
                          s.last_name,    -- Get last_name from students
                          g.grade_value   -- Get existing grade value from grades (will be NULL if no grade exists)
                       FROM
                          enrollments e
                       JOIN
                          students s ON e.student_code = s.student_code -- Join enrollments to students using student_code
                       LEFT JOIN
                          grades g ON e.enrollment_id = g.enrollment_id -- Left Join to grades using enrollment_id
                       WHERE
                          e.course_code = ?
                          AND e.semester = ?
                          AND e.academic_year = ?
                       ORDER BY
                          e.student_code ASC"; // Order the list by student code

    if ($stmt_students = $conn->prepare($query_students)) {
         // Bind parameters for the WHERE clause: course_code (s), semester (i), academic_year (i)
         $stmt_students->bind_param("sii", $selected_course_code, $selected_semester, $selected_academic_year);
         $stmt_students->execute();
         $result_students = $stmt_students->get_result();

         // Fetch all results into the array
         while ($row_student = $result_students->fetch_assoc()) {
             $enrolled_students[] = $row_student;
         }

         $stmt_students->close(); // Close the statement

    } else {
         echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงรายชื่อนิสิตที่ลงทะเบียน: ' . $conn->error . '</div>';
    }
}


// --- ส่วนของการแสดงผล HTML/JavaScript สำหรับเนื้อหาส่วนนี้ ---
?>


<?php if (empty($teacher_assignments)): ?>
    <div class="alert alert-info">คุณยังไม่ได้รับมอบหมายให้สอนรายวิชาใดๆ</div>
<?php else: ?>
    <form action="teacher_dashboard.php" method="GET">
        <input type="hidden" name="page" value="record_grades"> <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-6">
                <label for="select_assignment" class="form-label">เลือกรหัสวิชาที่ต้องการบันทึกผลการเรียน:</label>
                <select class="form-select" id="select_assignment" name="assignment_id" required>
                    <option value="">เลือกรหัสวิชา - ภาคเรียน - ปีการศึกษา</option>
                    <?php foreach ($teacher_assignments as $assignment): ?>
                         <option value="<?php echo htmlspecialchars($assignment['assignment_id']); ?>"
                                 <?php echo ($selected_assignment_id == $assignment['assignment_id']) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($assignment['course_code'] . ' - ' . $assignment['course_name'] . ' (ภาค ' . $assignment['semester'] . '/' . $assignment['academic_year'] . ')'); ?>
                         </option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="col-md-3">
                 <button type="submit" class="btn btn-primary">แสดงรายชื่อนิสิต</button>
             </div>
        </div>
    </form>

    <?php
    // --- แสดงรายชื่อนิสิตที่ลงทะเบียนสำหรับวิชาที่เลือก (ถ้ามี) ---
    // ตรวจสอบว่ามีรายวิชาที่เลือกและดึงข้อมูลมาได้แล้ว
    if ($selected_course_code !== null && $selected_semester !== null && $selected_academic_year !== null):
    ?>

        <h3 class="mt-4">รายชื่อนิสิตในวิชา <?php echo htmlspecialchars($selected_course_code . ' - ' . $selected_course_name); ?> (ภาค <?php echo htmlspecialchars($selected_semester); ?>/<?php echo htmlspecialchars($selected_academic_year); ?>)</h3>

        <?php if (empty($enrolled_students)): ?>
             <div class="alert alert-info">ไม่พบรายชื่อนิสิตที่ลงทะเบียนในรายวิชานี้ หรือเกิดข้อผิดพลาดในการดึงข้อมูลนิสิต</div>
        <?php else: ?>
            <form action="teacher_dashboard.php?page=record_grades" method="POST">
                 <input type="hidden" name="action" value="save_grades">
                 <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($selected_assignment_id); ?>">

                 <table class="table table-bordered table-striped mt-3">
                     <thead>
                         <tr>
                             <th>รหัสนิสิต</th>
                             <th>ชื่อ - นามสกุล</th>
                             <th>ผลการเรียน (เกรด)</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($enrolled_students as $student): ?>
                             <tr>
                                 <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                                 <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                 <td>
                                     <input type="text"
                                            name="grades[<?php echo htmlspecialchars($student['enrollment_id']); ?>]"
                                            class="form-control form-control-sm"
                                            value="<?php echo htmlspecialchars($student['grade_value'] ?? ''); ?>" placeholder="บันทึกเกรด"
                                            maxlength="10"> </td>
                             </tr>
                         <?php endforeach; ?>
                     </tbody>
                 </table>

                 <button type="submit" class="btn btn-success mt-3">บันทึกผลการเรียน</button>
            </form>
        <?php endif; ?> <?php endif; ?> <?php endif; ?> <?php
// --- จบส่วน HTML Output ---
// ไม่มีแท็กปิด ?>