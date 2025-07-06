<?php
ob_start();
// teacher_manage_courses.php
// ไฟล์นี้จะถูก include ใน teacher_dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// *** 2. PHP: Include ไฟล์กำหนดค่าฐานข้อมูล (ก่อนมี Output) ***
require_once '../config/db.php';

// *** 3. PHP: ตรวจสอบสิทธิ์การเข้าถึง (ก่อนมี Output) ***
$loggedInUserId = $_SESSION['user_id'] ?? null;
$loggedInUserRole = $_SESSION['user_role'] ?? null;
$loggedInUserName = $_SESSION['user_name'] ?? 'อาจารย์';
$loggedInStaffId = $_SESSION['staff_id'] ?? null;

if ($loggedInUserId === null || $loggedInUserRole !== 'Teachers') { // ใช้ 'Teachers' ตามที่พบใน admin_login_action.php
    header("Location: ../Public/login.php");
    exit();
}

// กำหนดชื่อและรหัสอาจารย์จาก Session
$teacher_name = $loggedInUserName;
$teacher_staff_id = $loggedInStaffId;


// *** 4. PHP: จัดการ POST Request (มาจาก Form ในหน้าที่ Include - ต้องอยู่ก่อน HTML) ***
// *** ย้าย Logic ส่วนนี้มาจาก teacher_manage_courses.php ***
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลแล้ว (ควรจะมีจาก require_once ด้านบนแล้ว)
     if (!isset($conn)) {
         // หากยังไม่มีการเชื่อมต่อ (กรณีที่ require_once ไม่ทำงานตามคาด)
        require_once '../config/db.php';
    }


    $action = $_POST['action'] ?? ''; // รับค่า action จากฟอร์ม

    switch ($action) {
        case 'add_assignment':
            $course_id = $_POST['course_code'] ?? null;
            $semester = $_POST['semester'] ?? null;
            $academic_year = $_POST['academic_year'] ?? null;

            if ($course_id && $semester && $academic_year) {
                 // --- ดึง course_code จากตาราง courses ---
                $course_code_to_insert = null;
                $query_course_code = "SELECT course_code FROM courses WHERE course_code = ?";
                if ($stmt_code = $conn->prepare($query_course_code)) { // <-- บรรทัดที่ 49 อาจอยู่ที่นี่ หรือถัดไปเล็กน้อย
                     $stmt_code->bind_param("s", $course_code);
                     $stmt_code->execute();
                     $result_code = $stmt_code->get_result();
                     if ($row_code = $result_code->fetch_assoc()) {
                         $course_code_to_insert = $row_code['course_code'];
                     }
                     $stmt_code->close();
                 } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งดึงรหัสวิชา: " . $conn->error;
                 }

                if ($course_code_to_insert === null) {
                     $_SESSION['error_message'] = "ไม่สามารถหารหัสวิชาสำหรับ ID ที่เลือกได้";
                } else {
                    // --- เพิ่มข้อมูลลงในตาราง teacher_course_assignments ---
                    $insert_sql = "INSERT INTO teacher_course_assignments (teacher_id, course_id, semester, academic_year, course_code) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt = $conn->prepare($insert_sql)) {
                        // สมมติว่า teacher_id เป็น VARCHAR, course_id, semester, academic_year เป็น INT, course_code เป็น VARCHAR
                        $stmt->bind_param("siiis", $loggedInStaffId, $course_id, $semester, $academic_year, $course_code_to_insert);

                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = "เพิ่มรายวิชาที่สอนสำเร็จ";
                        } else {
                            if ($conn->errno == 1062) {
                                 $_SESSION['error_message'] = "ไม่สามารถเพิ่มรายวิชาได้: อาจารย์ท่านนี้สอนวิชานี้ในภาคการศึกษาและปีการศึกษานี้อยู่แล้ว";
                            } else {
                                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเพิ่มรายวิชา: " . $conn->error;
                            }
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งเพิ่มรายวิชา: " . $conn->error;
                    }
                }
            } else {
                $_SESSION['error_message'] = "กรุณากรอกข้อมูลรายวิชาให้ครบ";
            }
            // Redirect กลับมาที่หน้านี้หลังจากทำ Action เสร็จ
                        header("Location:..teacher_dashboard.php?page=manage_courses"); 
            exit(); // *** Exit หลัง header() ***
            break;

        case 'edit_assignment':
            $assignment_id = $_POST['assignment_id'] ?? null;
            $course_id = $_POST['course_id'] ?? null;
            $semester = $_POST['semester'] ?? null;
            $academic_year = $_POST['academic_year'] ?? null;

             if ($assignment_id && $course_id && $semester && $academic_year) {

                 // --- ดึง course_code จากตาราง courses (สำหรับการแก้ไข) ---
                $course_code_to_update = null;
                $query_course_code = "SELECT course_code FROM courses WHERE course_id = ?";
                $stmt_code = $conn->prepare($query_course_code);
                 if ($stmt_code) {
                     $stmt_code->bind_param("i", $course_id);
                     $stmt_code->execute();
                     $result_code = $stmt_code->get_result();
                     if ($row_code = $result_code->fetch_assoc()) {
                         $course_code_to_update = $row_code['course_code'];
                     }
                     $stmt_code->close();
                 } else {
                     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งดึงรหัสวิชา (แก้ไข): " . $conn->error;
                 }

                if ($course_code_to_update === null) {
                     $_SESSION['error_message'] = "ไม่สามารถหารหัสวิชาสำหรับ ID ที่เลือกได้ (แก้ไข)";
                } else {

                    // --- แก้ไขข้อมูลในตาราง teacher_course_assignments ---
                     $update_sql = "UPDATE teacher_course_assignments SET course_id = ?, semester = ?, academic_year = ?, course_code = ? WHERE assignment_id = ? AND teacher_id = ?";
                    if ($stmt = $conn->prepare($update_sql)) {
                         $stmt->bind_param("iiissi", $course_id, $semester, $academic_year, $course_code_to_update, $assignment_id, $loggedInStaffId);
                        if ($stmt->execute()) {
                            if ($stmt->affected_rows > 0) {
                                 $_SESSION['success_message'] = "แก้ไขข้อมูลรายวิชาที่สอนสำเร็จ";
                            } else {
                                 if($conn->error){
                                     $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขรายวิชา: " . $conn->error;
                                 } else {
                                     $_SESSION['warning_message'] = "ไม่พบข้อมูลรายวิชาที่ต้องการแก้ไข หรือไม่มีการเปลี่ยนแปลงข้อมูล";
                                 }
                            }
                        } else {
                             $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการแก้ไขรายวิชา: " . $conn->error;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งแก้ไขรายวิชา: " . $conn->error;
                    }
                }
            } else {
                $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนสำหรับการแก้ไข";
            }
            header("Location:..teacher_dashboard.php?page=manage_courses");  // Redirect ไปยังหน้า success.php พร้อมส่ง assignment_id
            exit(); // *** Exit หลัง header() ***
            break;

        case 'delete_assignment':
            $assignment_id = $_POST['assignment_id'] ?? null;

            if ($assignment_id) {
                 // --- ลบข้อมูลในตาราง teacher_course_assignments ---
                 $delete_sql = "DELETE FROM teacher_course_assignments WHERE assignment_id = ? AND teacher_id = ?";
                 if ($stmt = $conn->prepare($delete_sql)) {
                    // สมมติว่า assignment_id เป็น INT, teacher_id เป็น INT/VARCHAR
                     $stmt->bind_param("is", $assignment_id, $loggedInStaffId); // ปรับ 's' ถ้า teacher_id เป็น VARCHAR
                    if ($stmt->execute()) {
                         if ($stmt->affected_rows > 0) {
                             $_SESSION['success_message'] = "ลบรายวิชาที่สอนสำเร็จ";
                        } else {
                             $_SESSION['warning_message'] = "ไม่พบรายวิชาที่ต้องการลบ หรือรายวิชานั้นไม่ได้อยู่ในความรับผิดชอบของคุณ";
                        }
                    } else {
                         $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรายวิชา: " . $conn->error;
                    }
                    $stmt->close();
                 } else {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งลบรายวิชา: " . $conn->error;
                 }

            } else {
                 $_SESSION['error_message'] = "ไม่พบข้อมูลรายวิชาที่ต้องการลบ";
            }
            header("Location:..teacher_dashboard.php?page=manage_courses"); // Redirect กลับมาที่หน้า manage_courses
            exit(); // *** Exit หลัง header() ***
            break;

        default:
            // Action ไม่ถูกต้อง หรือไม่มี action
            break;
    }
    // ไม่ต้องปิด $conn ที่นี่ เพราะจะนำไปใช้ในการดึงข้อมูลแสดงผลต่อในหน้าเดียวกัน
}

// --- ส่วนของการแสดงผล (ดึงข้อมูลรายวิชาที่อาจารย์สอนมาแสดง) ---

$teacher_courses = []; // อาร์เรย์สำหรับเก็บรายวิชาที่อาจารย์สอน

// ดึงข้อมูลรายวิชาที่อาจารย์ท่านนี้สอนจากฐานข้อมูล
// โดยเชื่อม (JOIN) ตาราง teacher_course_assignments กับตาราง courses
$query = "SELECT
             tca.assignment_id,
             c.course_code,
             c.course_name,
             tca.semester,
             tca.academic_year
          FROM
             teacher_course_assignments tca
          JOIN
             courses c ON tca.course_code = c.course_code
          WHERE
             tca.teacher_id = ?"; // กรองตาม teacher_id ของอาจารย์ที่ล็อกอิน

if ($stmt = $conn->prepare($query)) {
    // สมมติว่า teacher_id เป็น INT ใช้ 'i'
    // ถ้า teacher_id เป็น VARCHAR ใช้ 's'
    $stmt->bind_param("s", $loggedInStaffId); // ปรับ 's' ถ้า teacher_id เป็น VARCHAR
    $stmt->execute();
    $result = $stmt->get_result();

    // ดึงข้อมูลทั้งหมดเก็บไว้ในอาร์เรย์
    while ($row = $result->fetch_assoc()) {
        $teacher_courses[] = $row;
    }

    $stmt->close();
} else {
    // Handle database error if query preparation fails
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงข้อมูลรายวิชา: ' . $conn->error . '</div>';
}

// --- ดึงรายการรายวิชาทั้งหมดสำหรับ Dropdown ในฟอร์มเพิ่ม/แก้ไข ---
$all_courses = [];
$query_all_courses = "SELECT course_code, course_code, course_name FROM courses ORDER BY course_code";
if ($result_all = $conn->query($query_all_courses)) {
     while ($row_all = $result_all->fetch_assoc()) {
         $all_courses[] = $row_all;
     }
     $result_all->free(); // คืนหน่วยความจำ
} else {
     echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงรายการรายวิชา: ' . $conn->error . '</div>';
}

?>
<?php ob_end_flush(); ?>

<?php if (empty($teacher_courses)): ?>
    <div class="alert alert-info">ยังไม่มีรายวิชาที่คุณสอนอยู่ในระบบ</div>
<?php else: ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>รหัสวิชา</th>
                <th>ชื่อวิชา</th>
                <th>ภาคการศึกษา</th>
                <th>ปีการศึกษา</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teacher_courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['semester']); ?></td>
                    <td><?php echo htmlspecialchars($course['academic_year']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editCourseModal" data-assignment-id="<?php echo $course['assignment_id']; ?>" data-course-id="<?php echo /* ดึง course_id จริงๆ จาก DB */ 'N/A'; ?>" data-semester="<?php echo $course['semester']; ?>" data-academic-year="<?php echo $course['academic_year']; ?>">แก้ไข</button>

                        <form action="teacher_dashboard.php?page=manage_courses" method="POST" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายวิชานี้?');">
                            <input type="hidden" name="action" value="delete_assignment">
                            <input type="hidden" name="assignment_id" value="<?php echo $course['assignment_id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<h2>เพิ่มรายวิชาที่คุณสอน</h2>

<form action="teacher_dashboard.php?page=manage_courses" method="POST">
    <input type="hidden" name="action" value="add_assignment">
    <div class="row g-3 mb-3">
        <div class="col-md-5">
            <label for="course_code" class="form-label">รายวิชา:</label>
            <select class="form-select" id="course_code" name="course_code" required>
                <option value="">เลือกรหัสวิชา</option>
                <?php foreach ($all_courses as $c): ?>
                     <option value="<?php echo htmlspecialchars($c['course_code']); ?>"><?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
             <label for="semester" class="form-label">ภาคการศึกษา:</label>
            <select class="form-select" id="semester" name="semester" required>
                <option value="">เลือกภาค</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3 (ภาคฤดูร้อน)</option>
            </select>
        </div>
        <div class="col-md-3">
             <label for="academic_year" class="form-label">ปีการศึกษา (พ.ศ.):</label>
            <input type="number" class="form-control" id="academic_year" name="academic_year" value="<?php echo date('Y') + 543; /* ค่าเริ่มต้นเป็นปีปัจจุบัน พ.ศ. */ ?>" required min="2500">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">เพิ่มรายวิชา</button>
</form>

<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCourseModalLabel">แก้ไขรายวิชาที่สอน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="teacher_dashboard.php?page=manage_courses" method="POST">
          <input type="hidden" name="action" value="edit_assignment">
           <input type="hidden" name="assignment_id" id="edit_assignment_id"> <div class="modal-body">
              <div class="mb-3">
                  <label for="edit_course_id" class="form-label">รายวิชา:</label>
                   <select class="form-select" id="edit_course_id" name="id" required>
                       <option value="">เลือกรหัสวิชา</option>
                        <?php foreach ($all_courses as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?></option>
                       <?php endforeach; ?>
                   </select>
              </div>
              <div class="mb-3">
                   <label for="edit_semester" class="form-label">ภาคการศึกษา:</label>
                   <select class="form-select" id="edit_semester" name="semester" required>
                       <option value="">เลือกภาค</option>
                       <option value="1">1</option>
                       <option value="2">2</option>
                       <option value="3">3 (ภาคฤดูร้อน)</option>
                   </select>
              </div>
               <div class="mb-3">
                   <label for="edit_academic_year" class="form-label">ปีการศึกษา (พ.ศ.):</label>
                   <input type="number" class="form-control" id="edit_academic_year" name="academic_year" required min="2500">
               </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    var editCourseModal = document.getElementById('editCourseModal');
    editCourseModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget;

        // Extract info from data-bs-* attributes
        var assignmentId = button.getAttribute('data-assignment-id');
        var courseId = button.getAttribute('data-course-id'); // คุณต้องดึงค่า course_id มาให้ได้
        var semester = button.getAttribute('data-semester');
        var academicYear = button.getAttribute('data-academic-year');

        // Update the modal's content.
        var modalAssignmentIdInput = editCourseModal.querySelector('#edit_assignment_id');
        var modalCourseSelect = editCourseModal.querySelector('#edit_course_id');
        var modalSemesterSelect = editCourseModal.querySelector('#edit_semester');
        var modalAcademicYearInput = editCourseModal.querySelector('#edit_academic_year');

        modalAssignmentIdInput.value = assignmentId;
        // คุณต้องทำให้ dropdown course_id เลือกค่าที่ถูกต้องตาม courseId ที่ดึงมาได้
        // เช่น modalCourseSelect.value = courseId;
        modalSemesterSelect.value = semester;
        modalAcademicYearInput.value = academicYear;

        // TODO: ดึงข้อมูล course_id จริงๆ มาจากฐานข้อมูลเมื่อสร้างตารางแสดงผล
        // หรืออาจจะใช้วิธี AJAX เรียกข้อมูลเต็มของ assignment มาเติมใน modal
        // ตอนนี้ใช้ค่าที่ส่งมาเบื้องต้นก่อน
        console.log("Editing Assignment ID:", assignmentId);
        console.log("Course ID:", courseId); // ค่านี้อาจยังไม่ถูกต้อง
        console.log("Semester:", semester);
        console.log("Academic Year:", academicYear);
         modalCourseSelect.value = courseId; // ลองกำหนดค่าเลย (อาจไม่ทำงานถ้า courseId ไม่ตรงกับ option value)
    });
</script>