<?php
$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนสนใจโครงการ</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .wrapper { max-width: 720px; margin: 32px auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 8px 28px rgba(0,0,0,.08); }
        h1 { margin-top: 0; color: #333; }
        label { display: block; margin-bottom: 12px; color: #333; }
        input[type=text], input[type=email], input[type=tel], select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .full { grid-column: 1 / -1; }
        .consent { margin: 18px 0; }
        button { background: #806917; color: #fff; border: none; padding: 12px 18px; border-radius: 8px; cursor: pointer; }
        button:hover { opacity: .95; }
        .notice { margin: 16px 0; padding: 14px 18px; border-radius: 8px; background: #e6f7df; color: #1f5f22; }
        a.admin { display: inline-block; margin-top: 12px; color: #806917; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>ลงทะเบียนสนใจโครงการ</h1>
        <?php if ($success): ?>
            <div class="notice">ส่งข้อมูลเรียบร้อยแล้ว ขอบคุณครับ</div>
        <?php endif; ?>
        <form action="save-registration.php" method="post">
            <div class="row">
                <label>
                    ชื่อ *
                    <input type="text" name="Fname" required maxlength="100" />
                </label>
                <label>
                    นามสกุล *
                    <input type="text" name="Lname" required maxlength="100" />
                </label>
                <label>
                    เบอร์โทรศัพท์ *
                    <input type="tel" name="Tel" required maxlength="10" minlength="9" />
                </label>
                <label>
                    อีเมล *
                    <input type="email" name="Email" required maxlength="200" />
                </label>
                <label class="full">
                    แบบบ้านที่สนใจ *
                    <select name="ModelInterest" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <option value="Ritz Haven">Ritz Haven</option>
                        <option value="Ritz Eden">Ritz Eden</option>
                        <option value="Ritz Solace">Ritz Solace</option>
                    </select>
                </label>
                <label class="full">
                    งบประมาณในการซื้อ *
                    <select name="PriceInterest" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <option value="ต่ำกว่า 4.5 ล้านบาท">ต่ำกว่า 4.5 ล้านบาท</option>
                        <option value="4.51 - 5 ล้านบาท">4.51 - 5 ล้านบาท</option>
                        <option value="5.01 - 5.5 ล้านบาท">5.01 - 5.5 ล้านบาท</option>
                        <option value="5.51 - 6 ล้านบาท">5.51 - 6 ล้านบาท</option>
                        <option value="6.01 - 6.5 ล้านบาท">6.01 - 6.5 ล้านบาท</option>
                        <option value="6.51 - 7 ล้านบาท">6.51 - 7 ล้านบาท</option>
                        <option value="มากกว่า 7 ล้านบาท">มากกว่า 7 ล้านบาท</option>
                    </select>
                </label>
                <label class="full">
                    เรื่องที่ต้องการติดต่อ
                    <select name="AppointTime">
                        <option value="ไม่ได้ระบุ">-- กรุณาเลือก --</option>
                        <option value="สนใจเข้าชมบ้านตัวอย่าง">สนใจเข้าชมบ้านตัวอย่าง</option>
                        <option value="นัดหมายเข้าชมโครงการ">นัดหมายเข้าชมโครงการ</option>
                        <option value="ต้องการให้เซลล์ติดต่อกลับ">ต้องการให้เซลล์ติดต่อกลับ</option>
                        <option value="ลงทะเบียนร่วมงาน The ritz Grand Opening">ลงทะเบียนร่วมงาน The ritz Grand Opening</option>
                    </select>
                </label>
                <label class="full">
                    ช่วงเวลาที่สะดวกให้ติดต่อกลับ
                    <select name="TimeSlot">
                        <option value="ไม่ได้ระบุ">-- กรุณาเลือก --</option>
                        <option value="09.00 – 12.00">09.00 – 12.00</option>
                        <option value="12.00 – 13.00">12.00 – 13.00</option>
                        <option value="13.00 – 18.00">13.00 – 18.00</option>
                        <option value="18.00 – 19.00">18.00 – 19.00</option>
                    </select>
                </label>
            </div>
            <div class="consent">
                <label>
                    <input type="checkbox" name="consent[]" value="บริษัทฯ จะจัดเก็บข้อมูลของท่าน" required />
                    บริษัทฯ จะจัดเก็บข้อมูลของท่าน เพื่อการติดต่อแจ้งข้อมูลข่าวสารที่เกี่ยวข้องกับผลิตภัณฑ์ บริการของบริษัทฯ และนำเสนอโครงการที่น่าสนใจ
                </label>
            </div>
            <button type="submit">ลงทะเบียน</button>
        </form>
        <p><a class="admin" href="registration-admin.php?key=theritzadmin2026">ดูรายการลงทะเบียน (Admin)</a></p>
    </div>
</body>
</html>
