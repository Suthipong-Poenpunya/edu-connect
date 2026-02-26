// Simple Course Adder - No Cache Version
// Use this if you have IO errors with the original script

const firebaseConfig = {
    apiKey: "AIzaSyDnZ-sh1RhmND1_N_drLOi9uL4KUdFpB0Q",
    authDomain: "edu-connect-a8315.firebaseapp.com",
    projectId: "edu-connect-a8315",
    storageBucket: "edu-connect-a8315.firebasestorage.app",
    messagingSenderId: "33042525923",
    appId: "1:33042525923:web:be51fde4766b06fd3a9fd6",
    measurementId: "G-QGG90GGG3Y"
};

const courses = [
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
        description: 'พื้นฐานหลักการระบบฐานข้อมูล ข้อมูลและสารสนเทศ การจัดการสารสนเทศ',
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
        description: 'ธรรมชาติของผู้เรียนและความแตกต่างระหว่างบุคคล',
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
        description: 'ทฤษฎีหลักการทางไฟฟ้า-อิเล็กทรอนิกส์',
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
        description: 'แนวคิดเบื้องต้นของระบบคอมพิวเตอร์',
        teacher: 'อาจารย์ผู้สอน',
        room: 'ห้องเรียน'
    }
];

async function addCoursesNoCache() {
    console.log('🚀 Adding courses without cache...');
    
    try {
        // Check if Firebase is already initialized
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        
        const db = firebase.firestore();
        
        // Disable persistence completely
        await db.disableNetwork();
        
        for (const course of courses) {
            await db.collection('courses').doc(course.courseCode).set(course);
            console.log(`✅ Added: ${course.courseCode} - ${course.courseName}`);
        }
        
        // Re-enable network
        await db.enableNetwork();
        
        console.log('🎉 All courses added successfully!');
        console.log(`Total: ${courses.length} courses`);
        
    } catch (error) {
        console.error('❌ Error:', error);
        console.log('💡 Try using Firefox or Incognito mode');
    }
}

async function checkCourses() {
    try {
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        
        const db = firebase.firestore();
        const snapshot = await db.collection('courses').get();
        
        console.log(`📊 Found ${snapshot.size} courses:`);
        snapshot.forEach(doc => {
            const course = doc.data();
            console.log(`📚 ${course.courseCode} - ${course.courseName}`);
        });
        
    } catch (error) {
        console.error('❌ Error checking courses:', error);
    }
}

console.log('📋 Simple Course Adder loaded.');
console.log('💡 Run addCoursesNoCache() to add courses');
console.log('💡 Run checkCourses() to verify');

window.addCoursesNoCache = addCoursesNoCache;
window.checkCourses = checkCourses;
