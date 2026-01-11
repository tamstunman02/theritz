<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

$to = 'tatiya.wpt@gmail.com';
$subject = 'มีผู้ลงทะเบียนสนใจโครงการ';

$message = "
ชื่อ: {$_POST['Fname']}
นามสกุล: {$_POST['Lname']}
โทร: {$_POST['Tel']}
อีเมล: {$_POST['Email']}

แบบบ้าน: {$_POST['ModelInterest']}
งบประมาณ: {$_POST['PriceInterest']}
ช่วงเวลา: {$_POST['AppointTime']}
";

$headers = "Content-Type: text/plain; charset=UTF-8";

mail($to, $subject, $message, $headers);
