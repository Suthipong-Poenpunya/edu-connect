<?php
// teacher_manage_announcements.php - แสดงรายการประกาศที่อาจารย์สร้างขึ้นและจัดการ.
// ไฟล์นี้ถูก include โดย teacher_dashboard.php และคาดหวังว่าตัวแปร $conn, $loggedInStaffId, $teacher_name พร้อมใช้งาน
include '../include/functions.php'; // ฟังก์ชันที่ใช้ในการดึงข้อมูลประกาศ
// ตรวจสอบตัวแปรที่คาดว่าจะได้รับมาจาก teacher_dashboard.php
// การตรวจสอบนี้ช่วยยืนยันว่าไฟล์แม่มีการตั้งค่าที่ถูกต้อง
$loggedInStaffId = $_SESSION['staff_id'] ?? null;
$teacher_staff_id = $loggedInStaffId; // รหัสเจ้าหน้าที่/อาจารย์ที่ล็อกอินอยู่
$teacher_name = $_SESSION['user_name'] ?? 'อาจารย์'; // ชื่ออาจารย์จาก Session 
$idconn = $conn;

// --- PHP: ดึงข้อมูลประกาศที่อาจารย์ท่านนี้สร้างขึ้น ---

// เรียกใช้ฟังก์ชัน getAnnouncementsByTeacher
// ฟังก์ชันนี้ต้องถูกนิยามไว้ใน functions.php และต้องรับ $conn Parameter
// *** ส่ง $loggedInStaffId และตัวแปร $conn ที่ได้รับมา ให้ฟังก์ชัน ***
// บรรทัดนี้คือบรรทัดที่ 18 ตาม Error Stack Trace ล่าสุด
$teacher_announcements = getAnnouncements();


// ตรวจสอบผลลัพธ์จากฟังก์ชัน
if ($teacher_announcements === false) {
     // หากฟังก์ชันคืนค่า false แสดงว่าเกิด Error ในการคิวรี่ภายในฟังก์ชัน
     // ฟังก์ชัน getLastDBError ต้องถูกนิยามใน functions.php และรับ $conn
     echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงข้อมูลประกาศ: ' . getLastDBError($conn) . '</div>'; // <-- ส่ง $conn ที่นี่
     $teacher_announcements = []; // กำหนดเป็น Array ว่างเพื่อป้องกัน Error ใน Loop แสดงผล
}


// --- ส่วนของการแสดงผล HTML สำหรับเนื้อหาส่วนนี้ ---
// HTML ส่วนนี้จะแสดงผลภายใน <div> class="teacher-main-content" ของ teacher_dashboard.php
?>


<?php
// ข้อความแจ้งเตือนจาก $_SESSION จะถูกแสดงโดย teacher_dashboard.php
// แต่สามารถเช็คและแสดงที่นี่อีกครั้งได้ ถ้าต้องการให้ข้อความอยู่ใกล้เนื้อหา
// if (isset($_SESSION['success_message'])) { echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>'; unset($_SESSION['success_message']); }
// if (isset($_SESSION['error_message'])) { echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>'; unset($_SESSION['error_message']); }
// if (isset($_SESSION['warning_message'])) { echo '<div class="alert alert-warning">' . htmlspecialchars($_SESSION['warning_message']) . '</div>'; unset($_SESSION['warning_message']); }
?>


<?php if (empty($teacher_announcements)): ?>
    <div class="alert alert-info">คุณยังไม่ได้สร้างประกาศใดๆ</div>
<?php else: ?>
    <div class="form-section">
                <h2>เพิ่มประกาศใหม่</h2>
                <form action="../admin/teacher_edit_announcement.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="title">หัวข้อประกาศ:</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="details">รายละเอียด:</label>
                        <textarea class="form-control" id="details" name="details" rows="5" required></textarea>
                    </div>
                     <div class="form-group">
                        <label for="date">วันที่:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="media">สื่อ (URL หรือ Path):</label>
                        <input type="text" class="form-control" id="media" name="media">
                    </div>
                     <div class="form-group">
                        <label for="created_at">ผู้ประกาศ:</label> <input type="text" class="form-control" id="created_at" name="created_at" placeholder="เช่น งานทะเบียน" required> </div>
                    <button type="submit" class="btn btn-primary" style="background-color: rgb(222, 92, 142); margin-top: 15px;">เพิ่มประกาศ</button>
                </form>
            </div>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>หัวข้อ</th>
                <th>รายละเอียด</th>
                <th>วันที่ประกาศ</th>
                <th>สื่อ</th>
                <th>ผู้ประกาศ (User)</th>
                <th>สร้างโดย</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teacher_announcements as $announcement): ?>
                <tr>
                    <td><?php echo htmlspecialchars($announcement['id']); ?></td>
                    <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($announcement['details'])); ?></td> <td><?php echo htmlspecialchars($announcement['date']); ?></td>
                    <td>
                        <?php if (!empty($announcement['media'])): ?>
                            <a href="<?php echo htmlspecialchars($announcement['media']); ?>" target="_blank">ดูสื่อ</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($teacher_name); ?>
                    </td>
                    <td><?php echo htmlspecialchars($announcement['created_at']); ?></td>
                    <td class="action-buttons">
                        <a href="teacher_edit_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>" class="btn btn-warning btn-sm me-2">แก้ไข</a>

                        <form action="../Actions/teacher_announcements_action.php" method="post" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประกาศนี้?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>