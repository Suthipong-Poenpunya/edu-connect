# คู่มีการตั้งค่า Firebase สำหรับ EDU CU Connect

## ขั้นตอนที่ 1: สร้างโปรเจค Firebase

1. ไปที่ [Firebase Console](https://console.firebase.google.com/)
2. คลิก "เพิ่มโปรเจค" (Add project)
3. ตั้งชื่อโปรเจค (เช่น: "edu-cu-connect")
4. เลือก "Continue" และทำตามขั้นตอนจนเสร็จสิ้น

## ขั้นตอนที่ 2: เปิดใช้งาน Authentication

1. ใน Firebase Console ไปที่ "Authentication" ในเมนูด้านซ้าย
2. คลิก "Get started"
3. ในแท็บ "Sign-in method" ให้เปิดใช้งาน "Email/Password"
4. เปิดใช้งานและบันทึก

## ขั้นตอนที่ 3: เปิดใช้งาน Firestore Database

1. ใน Firebase Console ไปที่ "Firestore Database" ในเมนูด้านซ้าย
2. คลิก "Create database"
3. เลือก "Start in test mode" (เพื่อการพัฒนา)
4. เลือก Location และคลิก "Enable"

## ขั้นตอนที่ 4: ตั้งค่า Security Rules

ใน Firestore Database ไปที่แท็บ "Rules" และแทนที่ด้วย:

```
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    // Users can read and write their own documents
    match /students/{userId} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
    }
    
    // Admin can read all student data
    match /students/{userId} {
      allow read: if request.auth != null && 
        get(/databases/$(database)/documents/admins/$(request.auth.uid)).data.role == 'admin';
    }
    
    // Course data - public read, admin write
    match /courses/{courseId} {
      allow read: if true;
      allow write: if request.auth != null && 
        get(/databases/$(database)/documents/admins/$(request.auth.uid)).data.role == 'admin';
    }
    
    // Registration data - user can read/write their own, admin can read all
    match /registrations/{registrationId} {
      allow read, write: if request.auth != null && 
        (request.auth.uid == resource.data.userId || 
         get(/databases/$(database)/documents/admins/$(request.auth.uid)).data.role == 'admin');
    }
    
    // Admin collection
    match /admins/{adminId} {
      allow read, write: if request.auth != null && 
        get(/databases/$(database)/documents/admins/$(request.auth.uid)).data.role == 'admin';
    }
  }
}
```

## ขั้นตอนที่ 5: รับค่า Configuration

1. ใน Firebase Console ไปที่ "Project settings" (รูปเฟือง)
2. ในแท็บ "General" คลิก "Config" ในส่วน "Firebase SDK snippet"
3. คัดลอกค่า configuration ทั้งหมด

## ขั้นตอนที่ 6: อัปเดตค่า Configuration ในโปรเจค

แทนที่ค่าในไฟล์ `firebase-config.js` และทุกไฟล์ HTML ที่มี:

```javascript
const firebaseConfig = {
    apiKey: "AIzaSyDnZ-sh1RhmND1_N_drLOi9uL4KUdFpB0Q",
    authDomain: "edu-connect-a8315.firebaseapp.com",
    projectId: "edu-connect-a8315",
    storageBucket: "edu-connect-a8315.firebasestorage.app",
    messagingSenderId: "33042525923",
    appId: "1:33042525923:web:be51fde4766b06fd3a9fd6",
    measurementId: "G-QGG90GGG3Y"
  };
```

โดยแทนที่ค่าต่างๆ ด้วยค่าจาก Firebase Console ของคุณ

## ขั้นตอนที่ 7: สร้างข้อมูลตัวอย่างใน Firestore

สร้าง collection และ documents ต่อไปนี้ใน Firestore:

### 1. Collection: `courses`

```
courses/1
{
  "course_name": "FUND DATA SYS VIS",
  "course_code": "2766233",
  "credits": 3,
  "semester": "2/2567",
  "status": true,
  "total_seats": 30,
  "available_seats": 22,
  "day": "TH",
  "time": "09:00 - 11:00",
  "description": "พื้นฐานหลักการระบบฐานข้อมูล...",
  "course_id": 2766233
}
```

### 2. Collection: `admins`

```
admins/admin_user_id
{
  "email": "admin@chula.ac.th",
  "name": "Administrator",
  "role": "admin",
  "created_at": timestamp
}
```

## ขั้นตอนที่ 8: ทดสอบระบบ

1. เปิดไฟล์ `index.html` ในเว็บเบราว์เซอร์
2. ลองลงทะเบียนผู้ใช้ใหม่
3. ลองเข้าสู่ระบบ
4. ตรวจสอบว่าข้อมูลถูกเก็บใน Firestore ถูกต้อง

## โครงสร้างฐานข้อมูล Firestore

```
students/
  {student_code}/
    - student_code (string)
    - id_card (string)
    - prefix (string)
    - name (string)
    - surname (string)
    - faculty (string)
    - major (string)
    - class (string)
    - address (string)
    - phone (string)
    - email (string)
    - created_at (timestamp)
    - status (string)

courses/
  {course_id}/
    - course_name (string)
    - course_code (string)
    - credits (number)
    - semester (string)
    - status (boolean)
    - total_seats (number)
    - available_seats (number)
    - day (string)
    - time (string)
    - description (string)

registrations/
  {registration_id}/
    - userId (string)
    - courseId (string)
    - semester (string)
    - status (string)
    - registered_at (timestamp)

admins/
  {admin_id}/
    - email (string)
    - name (string)
    - role (string)
    - created_at (timestamp)
```

## การจัดการ Authentication

ระบบใช้ Firebase Authentication โดย:
- สร้าง email จากรหัสนิสิต (เช่น: 64000001@chula.ac.th)
- ใช้รหัสผ่านที่ผู้ใช้กำหนด
- เก็บข้อมูลเพิ่มเติมใน Firestore collection `students`

## การปรับใช้งาน (Deployment)

เมื่อพร้อมใช้งานจริง:
1. เปลี่ยน Security Rules เพื่อความปลอดภัย
2. ปิด Test mode ใน Firestore
3. ตั้งค่า Hosting บน Firebase หรือใช้ hosting service อื่น
4. เพิ่ม domain ที่อนุญาตใน Authentication

## การแก้ไขปัญหาที่พบบ่อย

1. **Permission denied**: ตรวจสอบ Security Rules ใน Firestore
2. **Auth/network-request-failed**: ตรวจสอบค่า configuration ให้ถูกต้อง
3. **User not found**: ตรวจสอบว่ามีการสร้าง user ใน Authentication และ Firestore แล้ว
4. **CORS errors**: ตรวจสอบว่าเรียกใช้ผ่าน web server ไม่ใช่ file://

## ติดต่อสนับสนุน

หากมีปัญหาในการตั้งค่า Firebase:
- ตรวจสอบ [Firebase Documentation](https://firebase.google.com/docs)
- ดูที่ [Firebase Console](https://console.firebase.google.com/)
- ตรวจสอบ Browser Console สำหรับ error messages
