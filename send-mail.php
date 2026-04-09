<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// ====== Debug: Log all POST data ======
$debug_log = __DIR__ . '/debug_log.txt';
$debug_entry = "[" . date('Y-m-d H:i:s') . "] POST data: " . json_encode($_POST, JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($debug_log, $debug_entry, FILE_APPEND);

// ====== Sanitize & Validate ======
$fname   = sanitize($_POST['Fname'] ?? '');
$lname   = sanitize($_POST['Lname'] ?? '');
$tel     = sanitize($_POST['Tel'] ?? '');
$email   = filter_var($_POST['Email'] ?? '', FILTER_SANITIZE_EMAIL);
$model   = sanitize($_POST['ModelInterest'] ?? '');
$price   = sanitize($_POST['PriceInterest'] ?? '');
$appoint = sanitize($_POST['AppointTime'] ?? '');
$timeslot = sanitize($_POST['TimeSlot'] ?? '');
$consent = isset($_POST['consent']);

// ====== Validate Required Fields ======
$errors = [];

if (empty($fname)) $errors[] = 'กรุณากรอกชื่อ';
if (empty($lname)) $errors[] = 'กรุณากรอกนามสกุล';
if (empty($tel)) $errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
if (empty($email)) $errors[] = 'กรุณากรอกอีเมล';
if (!$consent) $errors[] = 'กรุณายอมรับนโยบายความเป็นส่วนตัว';
if (empty($model) || $model === '') $errors[] = 'กรุณาเลือกแบบบ้านที่สนใจ';
if (empty($price) || $price === '') $errors[] = 'กรุณาเลือกงบประมาณในการซื้อ';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'อีเมลไม่ถูกต้อง']);
    exit;
}

// ====== Configuration ======
$gmail_email = ""; // *** เปลี่ยนเป็นอีเมล Gmail ของคุณ ***
$gmail_password = "@"; // *** ใส่ App Password จาก Google (https://myaccount.google.com/apppasswords) ***
$use_smtp = !empty($gmail_password); // ถ้าใส่ password ใช้ SMTP, ถ้าไม่ใช้ mail()

// ====== Prepare Email ======
$to = $gmail_email;
$subject = "มีผู้สนใจโครงการ THERITZ";

$message = "
ข้อมูลผู้สนใจ:
- ชื่อ: $fname $lname
- เบอร์โทร: $tel
- อีเมล: $email

ข้อมูลการสนใจ:
- แบบบ้าน: " . (!empty($model) ? $model : 'ไม่ได้ระบุ') . "
- งบประมาณ: " . (!empty($price) ? $price : 'ไม่ได้ระบุ') . "
- เรื่องติดต่อ: " . (!empty($appoint) && $appoint !== 'ไม่ได้ระบุ' ? $appoint : 'ไม่ได้ระบุ') . "
- ช่วงเวลา: " . (!empty($timeslot) && $timeslot !== 'ไม่ได้ระบุ' ? $timeslot : 'ไม่ได้ระบุ') . "

---
ส่งจาก: https://theritz-six.vercel.app
วันที่ส่ง: " . date('Y-m-d H:i:s') . "
";
ส่งจาก: https://www.theritzofficial.com/
";

// ====== Send Email via PHP mail() ======
$headers = "From: noreply@theritz.co.th\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$subject = "=?UTF-8?B?" . base64_encode("มีผู้สนใจโครงการ THERITZ") . "?=";

if (mail($to, $subject, $message, $headers)) {
    saveToLog($fname, $lname, $tel, $email, $model, $price);
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'ส่งข้อมูลเรียบร้อยแล้ว']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการส่งอีเมล']);
}

// ====== Helper Functions ======
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function saveToLog($fname, $lname, $tel, $email, $model, $price) {
    $log_file = __DIR__ . '/lead_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $fname $lname | $tel | $email | $model | $price\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
