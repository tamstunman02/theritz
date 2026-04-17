<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// 1. ตั้งค่า Telegram ของคุณ
$apiToken = "8773200753:AAHNogtwXXtx8_5cyZL97YEb3BVXPw2N-KE";
$chatId = "5263424338";

// 2. รับข้อมูลจากฟอร์ม (อิงตาม name ใน HTML ของคุณ)
function clean_post_value($key) {
    $value = $_POST[$key] ?? '';
    $value = trim($value);
    if ($value === '' || strtolower($value) === 'undefined') {
        return 'ไม่ระบุ';
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$fname    = clean_post_value('Fname');
$lname    = clean_post_value('Lname');
$tel      = clean_post_value('Tel');
$email    = clean_post_value('Email');
$model    = clean_post_value('ModelInterest');
$price    = clean_post_value('PriceInterest');
$appoint  = clean_post_value('AppointTime');
$timeslot = clean_post_value('TimeSlot');

$consent = isset($_POST['consent']) && is_array($_POST['consent']) ? implode(', ', array_map(function($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }, $_POST['consent'])) : 'ไม่ยอมรับ';

// 3. จัดรูปแบบข้อความ
$message = "🏠 <b>มีผู้สนใจโครงการ (The ritz ratchaburi)</b>\n";
$message .= "━━━━━━━━━━━━━━━\n";
$message .= "👤 <b>ชื่อ-นามสกุล:</b> $fname $lname\n";
$message .= "📞 <b>เบอร์โทร:</b> $tel\n";
$message .= "📧 <b>อีเมล:</b> $email\n";
$message .= "🏘️ <b>แบบบ้าน:</b> $model\n";
$message .= "💰 <b>งบประมาณ:</b> $price\n";
$message .= "📅 <b>เรื่องที่ติดต่อ:</b> $appoint\n";
$message .= "⏰ <b>ช่วงเวลาที่สะดวก:</b> $timeslot\n";
$message .= "📋 <b>ความยินยอม:</b> $consent\n";
$message .= "━━━━━━━━━━━━━━━\n";
$message .= "🌐 จากหน้าเว็บ: " . ($_SERVER['HTTP_REFERER'] ?? 'Direct');

// 4. ฟังก์ชันส่งข้อมูลไปยัง Telegram API
$url = "https://api.telegram.org/bot$apiToken/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// 5. ตอบกลับไปยังหน้าเว็บ
if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'ส่งข้อมูลเรียบร้อยแล้ว']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการส่งข้อมูล']);
}