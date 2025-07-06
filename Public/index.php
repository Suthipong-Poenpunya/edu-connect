<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDU CU Connect</title>
    <img src="../images/EDU-Logo.png" alt="Logo" style="max-width: 25%; height: auto;" class="logo">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        .logo {
            display: block; 
            margin: 0 auto 15px auto;
        }
        .Sponsor {
            display: block; 
            margin: 0 auto 15px auto;
            margin-top: 10px;
        }
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 800px;
        }
        h1 {
            color:rgb(222, 92, 142);
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 25px;
            background-color:rgb(222, 92, 142);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .cta-button:hover {
            background-color:rgb(222, 92, 142);
        }
        .admin-button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #F05023;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .admin-button:hover {
            background-color: #F05023;
        }
        .features {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .feature-item {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            width: 30%;
            min-width: 250px;
        }
        .feature-item h3 {
            color:rgb(222, 92, 142);
            margin-bottom: 10px;
        }
        footer {
            margin-top: 50px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9em;
        }
        t {
            margin-top: 20px;
            margin-bottom: -15px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>EDU CU Connect</h1>
        <p>ยินดีต้อนรับสู่ EDU CU Connect ระบบที่จัดการข้อมูลนิสิตต่างๆ ของนิสิตคณะครุศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย ได้อย่างสะดวกและรวดเร็ว</p>
        <div>
            <t>สำหรับนิสิต</t>
            <a href="/Public/LOGIN.php" class="cta-button">เข้าสู่ระบบ</a>
        </div>
            <t>สำหรับเจ้าหน้าที่หรืออาจารย์ เท่านั้น</t>
            <a href="/admin/admin-login.php" class="admin-button">เข้าสู่ระบบสำหรับเจ้าหน้าที่</a>
        <div class="features">
            <div class="feature-item">
                <h3>การลงทะเบียน</h3>
                <p>ลงทะเบียนรายวิชาและจัดการตารางเรียนของคุณ</p>
            </div>
            <div class="feature-item">
                <h3>ข้อมูลนิสิต</h3>
                <p>ตรวจสอบและแก้ไขข้อมูลส่วนตัวของคุณ</p>
            </div>
            <div class="feature-item">
                <h3>ประกาศ</h3>
                <p>ติดตามข่าวสารและประกาศสำคัญจากมหาวิทยาลัย</p>
            </div>
        </div>
        <footer>
            <p href="https://www.chula.ac.th/th/">© 2024 จุฬาลงกรณ์มหาวิทยาลัย สงวนลิขสิทธิ์.</p>
            <p>เว็บไซต์นี้จัดทำโดย นายสุทธิพงษ์ เปิ้นปัญญา นิสิตชั้นปีที่ 1 วิชาเอกเทคโนโลยีการศึกษา</p>
            <p>เป็นส่วนหนึ่งของรายวิชา 2766233 FUND DATA SYS VIS</p>
            <p>ภาควิชาเทคโนโลยีและสื่อสารการศึกษา คณะครุศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย</p>
        </footer>
    </div>
    <a href="https://www.chula.ac.th" target="_blank">
        <img src="../images/Spon-2766223.png" alt="Sponsor" style="max-width: 600px; height: auto;" class="Sponsor">
     </a>
</body>
</html>
