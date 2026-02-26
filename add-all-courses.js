// Add all courses from SQL database to Firestore
// Run this script in browser console after Firebase is initialized

const allCourses = [
    {
        courseCode: '2766233',
        courseName: 'FUND DATA SYS VIS',
        credits: 3,
        semester: '2/2567',
        year: '2567',
        faculty: 'คณะวิทยาศาสตร์',
        major: 'วิทยาการคอมพิวเตอร์',
        availableSeats: 30,
        enrolledStudents: 22,
        day: 'TH',
        time: '09:00 - 11:00',
        description: 'พื้นฐานหลักการระบบฐานข้อมูล ข้อมูลและสารสนเทศ การจัดการสารสนเทศ แนวคิดเกี่ยวกับฐานข้อมูลและระบบการจัดการฐานข้อมูล การออกแบบและพัฒนาฐานข้อมูลเบื้องต้นด้วยโปรแกรมการประยุกต์และความรู้พื้นฐานในการโปรแกรมภาษาสำหรับเรียกใช้ฐานข้อมูล(เอส.คิว.แอล) กับการประมวลผลข้อมูล และการทำข้อมูลให้เป็นภาพ',
        teacher: 'อาจารย์ผู้สอน',
        room: 'ห้องเรียน'
    },
    {
        courseCode: '2759151',
        courseName: 'FOUND PSY ED',
        credits: 2,
        semester: '2/2567',
        year: '2567',
        faculty: 'คณะครุศาสตร์',
        major: 'จิตวิทยาการศึกษา',
        availableSeats: 30,
        enrolledStudents: 3,
        day: 'MON',
        time: '08:00 - 10:00',
        description: 'ธรรมชาติของผู้เรียนและความแตกต่างระหว่างบุคคล หลักการ แนวคิด และทฤษฎีทางจิตวิทยาที่เกี่ยวข้องกับ การรับรู้ การเรียนรู้ การประมวลข้อมูลและการถ่ายโยงการเรียนรู้ เชาวน์ปัญญาและความถนัด พัฒนาการของผู้เรียนทุกช่วงวัย ลักษณะของผู้เรียนที่มีความต้องการพิเศษ การบูรณาการทฤษฎีทางจิตวิทยาเพื่อการวิเคราะห์และเข้าใจลักษณะของผู้เรียนเป็นรายบุคคล ทั้งผู้เรียนทั่วไปและผู้เรียนที่มีความต้องการพิเศษ ความเข้าใจในอารมณ์ความรู้สึกของผู้อื่น การมีความคิดเชิงบวก การมีวุฒิภาวะทางอารมณ์และทางสังคม',
        teacher: 'อาจารย์ผู้สอน',
        room: 'ห้องเรียน'
    },
    {
        courseCode: '2765135',
        courseName: 'FOUND ELEC EDU',
        credits: 3,
        semester: '2/2567',
        year: '2567',
        faculty: 'คณะครุศาสตร์',
        major: 'เทคโนโลยีการศึกษา',
        availableSeats: 30,
        enrolledStudents: 20,
        day: 'MON',
        time: '16:00 - 18:00',
        description: 'ทฤษฎีหลักการทางไฟฟ้า-อิเล็กทรอนิกส์ ความรู้เบื้องต้นเกี่ยวกับวัสดุ อุปกรณ์ เครื่องมือพื้นฐานด้านไฟฟ้า-อิเล็กทรอนิกส์ กลไก-ไฟฟ้า อิเล็กทรอนิกส์ ทฤษฎีแนวโน้มการศึกษาด้านไฟฟ้า-อิเล็กทรอนิกส์ ระบบเทคโนโลยีที่ทันสมัย กระบวนการออกแบบเชิงวิศวกรรมเพื่อนำไปใช้ในการปฏิบัติงานด้านเทคโนโลยีการศึกษา การศึกษาร่วมสมัย วิธีการเรียนทางอิเล็กทรอนิกส์ในรูปแบบต่าง ๆ เน้นปฏิบัติการ การทำโครงงาน การนำความรู้ด้านไฟฟ้า-อิเล็กทรอนิกส์ไปประยุกต์ใช้ในการกำหนดคุณลักษณะเฉพาะของสื่อการศึกษา และแนวคิดพื้นฐานและการประยุกต์ใช้ Internet of Things (IOT) ในการสร้างโครงงานและนวัตกรรมทางการศึกษา',
        teacher: 'อาจารย์ผู้สอน',
        room: 'ห้องเรียน'
    },
    {
        courseCode: '2301170',
        courseName: 'COMP PROG',
        credits: 3,
        semester: '2/2567',
        year: '2567',
        faculty: 'คณะวิทยาศาสตร์',
        major: 'วิทยาการคอมพิวเตอร์',
        availableSeats: 200,
        enrolledStudents: 38,
        day: 'MON',
        time: '10:00 - 11:00',
        description: 'แนวคิดเบื้องต้นของระบบคอมพิวเตอร์ แนวคิดเบื้องต้นของโปรแกรม จรรยาบรรณและความปลอดภัยทางด้านคอมพิวเตอร์ การประยุกต์',
        teacher: 'อาจารย์ผู้สอน',
        room: 'ห้องเรียน'
    }
];

async function addAllCoursesToFirestore() {
    console.log('🚀 Adding all courses to Firestore...');
    
    try {
        // Initialize Firebase with no cache
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const db = firebase.firestore();
        
        // Disable cache to avoid IO errors
        db.settings({
            cacheSizeBytes: firebase.firestore.CACHE_SIZE_UNLIMITED
        });
        
        for (const course of allCourses) {
            await db.collection('courses').doc(course.courseCode).set(course);
            console.log(`✅ Added course: ${course.courseCode} - ${course.courseName}`);
        }
        
        console.log('🎉 All courses added successfully!');
        console.log(`Total courses added: ${allCourses.length}`);
        
    } catch (error) {
        console.error('❌ Error adding courses:', error);
    }
}

// Function to verify courses were added
async function verifyCourses() {
    console.log('🔍 Verifying courses in Firestore...');
    
    try {
        // Initialize Firebase with no cache
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const db = firebase.firestore();
        
        // Disable cache to avoid IO errors
        db.settings({
            cacheSizeBytes: firebase.firestore.CACHE_SIZE_UNLIMITED
        });
        
        const coursesSnapshot = await db.collection('courses').get();
        
        console.log(`📊 Total courses in Firestore: ${coursesSnapshot.size}`);
        
        coursesSnapshot.forEach(doc => {
            const course = doc.data();
            console.log(`📚 ${course.courseCode} - ${course.courseName} (${course.credits} credits)`);
        });
        
    } catch (error) {
        console.error('❌ Error verifying courses:', error);
    }
}

// Auto-run when script is loaded
console.log('📋 Course data loaded. Run addAllCoursesToFirestore() to add courses to Firestore.');
console.log('🔍 Run verifyCourses() to check existing courses.');

// Export functions for manual execution
window.addAllCoursesToFirestore = addAllCoursesToFirestore;
window.verifyCourses = verifyCourses;
