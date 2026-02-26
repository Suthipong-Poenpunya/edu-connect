// สคริปต์เพิ่มข้อมูลรายวิชาตัวอย่างใน Firestore
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

// ข้อมูลรายวิชาตัวอย่าง
const sampleCourses = [
    {
        course_name: "FUND DATA SYS VIS",
        course_code: "2766233",
        credits: 3,
        semester: "2/2567",
        status: true,
        total_seats: 30,
        available_seats: 22,
        day: "TH",
        time: "09:00 - 11:00",
        description: "พื้นฐานหลักการระบบฐานข้อมูล ข้อมูลและสารสนเทศ การจัดการสารสนเทศ แนวคิดเกี่ยวกับฐานข้อมูลและระบบการจัดการฐานข้อมูล การออกแบบและพัฒนาฐานข้อมูลเบื้องต้นด้วยโปรแกรมการประยุกต์และความรู้พื้นฐานในการโปรแกรมภาษาสำหรับเรียกใช้ฐานข้อมูล(เอส.คิว.แอล) กับการประมวลผลข้อมูล และการทำข้อมูลให้เป็นภาพ",
        course_id: 2766233
    },
    {
        course_name: "เทคโนโลยีและนวัตกรรมทางการศึกษา",
        course_code: "2766223",
        credits: 3,
        semester: "2/2567",
        status: true,
        total_seats: 25,
        available_seats: 18,
        day: "MO",
        time: "13:00 - 16:00",
        description: "การศึกษาแนวคิด หลักการ และกระบวนการนวัตกรรมทางการศึกษา การประยุกต์ใช้เทคโนโลยีสารสนเทศและการสื่อสารในการพัฒนานวัตกรรมทางการศึกษา",
        course_id: 2766223
    },
    {
        course_name: "การวิจัยทางการศึกษา",
        course_code: "2766243",
        credits: 3,
        semester: "2/2567",
        status: true,
        total_seats: 20,
        available_seats: 15,
        day: "WE",
        time: "09:00 - 12:00",
        description: "หลักการและกระบวนการวิจัยทางการศึกษา การเลือกปัญหาและวางแผนการวิจัย การเก็บรวบรวมและวิเคราะห์ข้อมูล การเขียนรายงานวิจัย",
        course_id: 2766243
    },
    {
        course_name: "การออกแบบการสอนแบบบูรณาการ",
        course_code: "2766253",
        credits: 3,
        semester: "2/2567",
        status: true,
        total_seats: 35,
        available_seats: 28,
        day: "TU",
        time: "14:00 - 17:00",
        description: "หลักการและกระบวนการออกแบบการสอนแบบบูรณาการ การวิเคราะห์และสังเคราะห์การสอน การประเมินผลการสอนแบบบูรณาการ",
        course_id: 2766253
    },
    {
        course_name: "การประเมินผลการเรียนรู้",
        course_code: "2766263",
        credits: 3,
        semester: "2/2567",
        status: true,
        total_seats: 30,
        available_seats: 25,
        day: "FR",
        time: "10:00 - 13:00",
        description: "หลักการและวิธีการประเมินผลการเรียนรู้ การสร้างเครื่องมือประเมิน การวิเคราะห์และใช้ข้อมูลผลการประเมินเพื่อพัฒนาการเรียนการสอน",
        course_id: 2766263
    }
];

async function addSampleCourses() {
    try {
        console.log('🔄 กำลังเพิ่มข้อมูลรายวิชาตัวอย่าง...');
        
        for (let i = 0; i < sampleCourses.length; i++) {
            const course = sampleCourses[i];
            const courseId = (i + 1).toString();
            
            await db.collection('courses').doc(courseId).set(course);
            console.log(`✅ เพิ่มรายวิชา: ${course.course_code} - ${course.course_name}`);
        }
        
        console.log('🎉 เพิ่มข้อมูลรายวิชาตัวอย่างเรียบร้อยแล้ว!');
        console.log(`📚 รวม ${sampleCourses.length} รายวิชา`);
        
        // แสดงรายการรายวิชาที่เพิ่ม
        console.log('\n📋 รายการรายวิชา:');
        sampleCourses.forEach((course, index) => {
            console.log(`${index + 1}. ${course.course_code} - ${course.course_name}`);
            console.log(`   วันเวลา: ${course.day} ${course.time}`);
            console.log(`   ที่นั่งว่าง: ${course.available_seats}/${course.total_seats}`);
        });
        
    } catch (error) {
        console.error('❌ เกิดข้อผิดพลาด:', error);
    }
}

// เรียกใช้ฟังก์ชัน
addSampleCourses();
