<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อมูลสำเร็จ</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="alert">
        <h2>บันทึกข้อมูลสำเร็จ!</h2>
        <p>ข้อมูลของคุณได้รับการบันทึกเรียบร้อยแล้ว</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "../Public/dashboard.php";
        }, 2000); // หน่วงเวลา 2000 มิลลิวินาที (2 วินาที)
    </script>
</body>
</html>