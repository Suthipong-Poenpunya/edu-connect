// สคริปต์สร้างข้อมูลตัวอย่างใน Firestore
// รันใน Browser Console หลังจากเปิด index.html

const firebaseConfig = {
    apiKey: "AIzaSyDnZ-sh1RhmND1_N_drLOi9uL4KUdFpB0Q",
    authDomain: "edu-connect-a8315.firebaseapp.com",
    projectId: "edu-connect-a8315",
    storageBucket: "edu-connect-a8315.firebasestorage.app",
    messagingSenderId: "33042525923",
    appId: "1:33042525923:web:be51fde4766b06fd3a9fd6",
    measurementId: "G-QGG90GGG3Y"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const db = firebase.firestore();

// สร้างข้อมูลตัวอย่าง
async function setupSampleData() {
    try {
        // สร้างข้อมูลนิสิตตัวอย่าง
        await db.collection('students').doc('64000001').set({
            student_code: '64000001',
            id_card: '1234567890123',
            prefix: 'นาย',
            name: 'สมชาย',
            surname: 'ใจดี',
            faculty: 'ครุศาสตร์',
            major: 'เทคโนโลยีการศึกษา',
            class: '1',
            address: '123 ถนนพระราม 4 เขตพญาไท กรุงเทพฯ 10400',
            phone: '0812345678',
            email: 'somchai@student.chula.ac.th',
            created_at: firebase.firestore.FieldValue.serverTimestamp(),
            status: 'active'
        });

        // สร้างข้อมูลรายวิชาตัวอย่าง
        await db.collection('courses').doc('1').set({
            course_name: 'FUND DATA SYS VIS',
            course_code: '2766233',
            credits: 3,
            semester: '2/2567',
            status: true,
            total_seats: 30,
            available_seats: 22,
            day: 'TH',
            time: '09:00 - 11:00',
            description: 'พื้นฐานหลักการระบบฐานข้อมูล ข้อมูลและสารสนเทศ การจัดการสารสนเทศ แนวคิดเกี่ยวกับฐานข้อมูลและระบบการจัดการฐานข้อมูล การออกแบบและพัฒนาฐานข้อมูลเบื้องต้นด้วยโปรแกรมการประยุกต์และความรู้พื้นฐานในการโปรแกรมภาษาสำหรับเรียกใช้ฐานข้อมูล(เอส.คิว.แอล) กับการประมวลผลข้อมูล และการทำข้อมูลให้เป็นภาพ',
            course_id: 2766233
        });

        // สร้างข้อมูลแอดมินตัวอย่าง
        await db.collection('admins').doc('admin123').set({
            email: 'admin@chula.ac.th',
            name: 'Administrator',
            role: 'admin',
            created_at: firebase.firestore.FieldValue.serverTimestamp()
        });

        console.log('✅ สร้างข้อมูลตัวอย่างเรียบร้อยแล้ว!');
        console.log('📧 สามารถล็อกอินด้วย:');
        console.log('   - รหัสนิสิต: 64000001');
        console.log('   - รหัสผ่าน: password123');
        console.log('   - อีเมล: 64000001@chula.ac.th');
        console.log('👨‍💼 แอดมิน:');
        console.log('   - อีเมล: admin@chula.ac.th');
        console.log('   - รหัสผ่าน: admin123');

    } catch (error) {
        console.error('❌ เกิดข้อผิดพลาด:', error);
    }
}

// เรียกใช้ฟังก์ชัน
setupSampleData();
