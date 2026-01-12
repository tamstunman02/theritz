<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// ====== รับค่าจากฟอร์ม ======
$fname   = $_POST['Fname'] ?? '';
$lname   = $_POST['Lname'] ?? '';
$tel     = $_POST['Tel'] ?? '';
$email   = $_POST['Email'] ?? '';
$model   = $_POST['ModelInterest'] ?? '';
$price   = $_POST['PriceInterest'] ?? '';
$time    = $_POST['AppointTime'] ?? '';

// ====== อีเมลผู้รับ ======
$to = "tamstunman02@gmail.com"; // เปลี่ยนเป็นอีเมลที่ต้องการรับ
$subject = "มีผู้สนใจโครงการ (ฟอร์มลงทะเบียน)";

// ====== เนื้อหาเมล ======
$message = "
ชื่อ: $fname $lname
เบอร์โทร: $tel
อีเมล: $email

แบบบ้านที่สนใจ: $model
งบประมาณ: $price
ช่วงเวลาที่สะดวกติดต่อ: $time
";

// ====== Header ======
$headers = "From: Website theritz-six.vercel.app\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";

// ====== ส่งอีเมล ======
if (mail($to, $subject, $message, $headers)) {
    echo "ส่งข้อมูลเรียบร้อยแล้ว";
} else {
    echo "ไม่สามารถส่งอีเมลได้";
}
