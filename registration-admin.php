<?php
const ADMIN_KEY = 'theritzadmin2026';
const STORAGE_FILE = __DIR__ . '/registrations.txt';

function load_records() {
    if (!file_exists(STORAGE_FILE)) {
        return [];
    }
    $lines = file(STORAGE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $records = [];
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if (is_array($data) && isset($data['id'])) {
            $records[] = $data;
        }
    }
    return $records;
}

function save_records(array $records) {
    $lines = array_map(function ($record) {
        return json_encode($record, JSON_UNESCAPED_UNICODE);
    }, $records);
    file_put_contents(STORAGE_FILE, implode(PHP_EOL, $lines) . (count($lines) ? PHP_EOL : ''), LOCK_EX);
}

function sanitize_text($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$key = $_GET['key'] ?? $_POST['key'] ?? '';
if ($key !== ADMIN_KEY) {
    http_response_code(403);
    echo '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>กรุณาใส่รหัสที่ถูกต้องในพารามิเตอร์ key</p></body></html>';
    exit;
}

$records = load_records();
$editId = $_GET['action'] === 'edit' ? ($_GET['id'] ?? '') : '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    if ($action === 'delete' && $id !== '') {
        $records = array_values(array_filter($records, function ($row) use ($id) {
            return ($row['id'] ?? '') !== $id;
        }));
        save_records($records);
        $message = 'ลบข้อมูลเรียบร้อยแล้ว';
    }
    if ($action === 'update' && $id !== '') {
        foreach ($records as &$row) {
            if (($row['id'] ?? '') === $id) {
                $row['Fname'] = sanitize_text($_POST['Fname'] ?? $row['Fname']);
                $row['Lname'] = sanitize_text($_POST['Lname'] ?? $row['Lname']);
                $row['Tel'] = sanitize_text($_POST['Tel'] ?? $row['Tel']);
                $row['Email'] = sanitize_text($_POST['Email'] ?? $row['Email']);
                $row['ModelInterest'] = sanitize_text($_POST['ModelInterest'] ?? $row['ModelInterest']);
                $row['PriceInterest'] = sanitize_text($_POST['PriceInterest'] ?? $row['PriceInterest']);
                $row['AppointTime'] = sanitize_text($_POST['AppointTime'] ?? $row['AppointTime']);
                $row['TimeSlot'] = sanitize_text($_POST['TimeSlot'] ?? $row['TimeSlot']);
                $row['consent'] = sanitize_text($_POST['consent'] ?? $row['consent']);
                break;
            }
        }
        unset($row);
        save_records($records);
        $message = 'แก้ไขข้อมูลเรียบร้อยแล้ว';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรายการลงทะเบียน</title>
    <style>
        :root {
            color-scheme: light;
            font-family: 'Inter', Arial, sans-serif;
            color: #1f2937;
            background: #f3f4f6;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; }
        body { background: #f3f4f6; display: flex; flex-direction: column; }
        .container { flex: 1; margin: 0; background: #ffffff; padding: 28px; width: 100%; display: flex; flex-direction: column; }
        h1 { margin-top: 0; margin-bottom: 8px; font-size: clamp(1.6rem, 2vw, 2.4rem); letter-spacing: -0.02em; }
        p { line-height: 1.7; color: #4b5563; }
        .summary { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .summary span { color: #2563eb; font-weight: 600; }
        .table-responsive { overflow-x: auto; overflow-y: auto; margin-top: 14px; flex: 1; }
        .records-table { width: 100%; border-collapse: collapse; }
        .records-table th, .records-table td { padding: 12px 14px; border: 1px solid #e5e7eb; vertical-align: top; }
        .records-table th { background: #f8fafc; color: #374151; font-weight: 700; text-align: left; white-space: nowrap; }
        .records-table tbody tr { transition: background-color .2s ease; }
        .records-table tbody tr:nth-child(odd) { background: #fcfcfd; }
        .records-table tbody tr:hover { background: #f1f5f9; }
        .actions { display: flex; gap: 8px; flex-wrap: nowrap; align-items: center; justify-content: center; }
        .actions form { display: inline-block; margin: 0; flex: 1; }
        .button { display: flex; align-items: center; justify-content: center; gap: 6px; height: 42px; padding: 0 14px; border: none; border-radius: 999px; font-weight: 600; color: #ffffff; text-decoration: none; cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; flex: 1; }
        .button:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12); }
        .button.edit { background: #2563eb; }
        .button.delete { background: #dc2626; }
        .button.save { background: #047857; }
        .notice { margin-top: 18px; padding: 16px 18px; background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 16px; color: #155e75; }
        .edit-form { margin-top: 24px; background: #ffffff; padding: 22px; border: 1px solid #e5e7eb; border-radius: 20px; }
        .edit-form h2 { margin-top: 0; margin-bottom: 16px; font-size: 1.25rem; }
        label { display: block; margin-bottom: 14px; color: #334155; font-size: 0.98rem; }
        input[type=text], input[type=email], input[type=tel], select { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 14px; background: #f8fafc; color: #0f172a; font-size: 0.95rem; }
        input[type=text]:focus, input[type=email]:focus, input[type=tel]:focus, select:focus { outline: none; border-color: #2563eb; background: #ffffff; box-shadow: 0 0 0 4px rgba(59,130,246,0.12); }
        .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .button.save { margin-top: 8px; }
        @media (max-width: 992px) {
            .container { padding: 20px; }
        }
        @media (max-width: 760px) {
            .container { padding: 18px; }
            .summary { flex-direction: column; align-items: flex-start; }
            .records-table th, .records-table td { padding: 10px 12px; }
            .grid-2 { grid-template-columns: 1fr; }
            .actions { flex-direction: column; align-items: stretch; }
            .button { width: 100%; }
            .records-table tbody tr { display: block; border-radius: 18px; border: 1px solid #e5e7eb; margin-bottom: 16px; background: #ffffff; }
            .records-table tbody tr td, .records-table tbody tr th { display: flex; justify-content: space-between; align-items: flex-start; width: 100%; border: none; padding: 10px 14px; }
            .records-table tbody tr td { border-top: 1px solid #e5e7eb; }
            .records-table tbody tr td:first-child { border-top: none; }
            .records-table tbody tr td::before {
                content: '';
                display: block;
                width: 100%;
                margin-bottom: 6px;
                color: #475569;
                font-weight: 700;
            }
            .records-table tbody tr td:nth-of-type(1)::before { content: 'เวลาลงทะเบียน'; }
            .records-table tbody tr td:nth-of-type(2)::before { content: 'ชื่อ'; }
            .records-table tbody tr td:nth-of-type(3)::before { content: 'เบอร์'; }
            .records-table tbody tr td:nth-of-type(4)::before { content: 'อีเมล'; }
            .records-table tbody tr td:nth-of-type(5)::before { content: 'แบบบ้าน'; }
            .records-table tbody tr td:nth-of-type(6)::before { content: 'งบประมาณ'; }
            .records-table tbody tr td:nth-of-type(7)::before { content: 'เรื่อง'; }
            .records-table tbody tr td:nth-of-type(8)::before { content: 'เวลาสะดวก'; }
            .records-table tbody tr td:nth-of-type(9)::before { content: 'ความยินยอม'; }
            .records-table tbody tr td:nth-of-type(10)::before { content: 'จัดการ'; }
            .records-table thead { display: none; }
            .table-responsive { margin-top: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>จัดการรายการลงทะเบียน</h1>
        <?php if ($message): ?>
            <div class="notice"><?php echo sanitize_text($message); ?></div>
        <?php endif; ?>
        <p>จำนวนรายการทั้งหมด: <?php echo count($records); ?></p>
        <?php if (empty($records)): ?>
            <p>ยังไม่มีข้อมูลลงทะเบียน</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>เวลาลงทะเบียน</th>
                        <th>ชื่อ</th>
                        <th>เบอร์</th>
                        <th>อีเมล</th>
                        <th>แบบบ้าน</th>
                        <th>งบประมาณ</th>
                        <th>เรื่อง</th>
                        <th>เวลาสะดวก</th>
                        <th>ความยินยอม</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo sanitize_text($record['created_at'] ?? ''); ?></td>
                            <td><?php echo sanitize_text(($record['Fname'] ?? '') . ' ' . ($record['Lname'] ?? '')); ?></td>
                            <td><?php echo sanitize_text($record['Tel'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['Email'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['ModelInterest'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['PriceInterest'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['AppointTime'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['TimeSlot'] ?? ''); ?></td>
                            <td><?php echo sanitize_text($record['consent'] ?? ''); ?></td>
                            <td class="actions">
                                <a class="button edit" href="registration-admin.php?action=edit&id=<?php echo urlencode($record['id']); ?>&key=<?php echo urlencode(ADMIN_KEY); ?>">แก้ไข</a>
                                <form method="post" onsubmit="return confirm('แน่ใจหรือไม่ที่จะลบข้อมูลนี้?');">
                                    <input type="hidden" name="key" value="<?php echo sanitize_text(ADMIN_KEY); ?>" />
                                    <input type="hidden" name="action" value="delete" />
                                    <input type="hidden" name="id" value="<?php echo sanitize_text($record['id']); ?>" />
                                    <button type="submit" class="button delete">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>

        <?php if ($editId):
            $editRecord = null;
            foreach ($records as $record) {
                if (($record['id'] ?? '') === $editId) {
                    $editRecord = $record;
                    break;
                }
            }
            if ($editRecord): ?>
                <div class="edit-form">
                    <h2>แก้ไขข้อมูล</h2>
                    <form method="post">
                        <input type="hidden" name="key" value="<?php echo sanitize_text(ADMIN_KEY); ?>" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" value="<?php echo sanitize_text($editRecord['id']); ?>" />
                        <div class="grid-2">
                            <label>ชื่อ<br /><input type="text" name="Fname" value="<?php echo sanitize_text($editRecord['Fname'] ?? ''); ?>" required /></label>
                            <label>นามสกุล<br /><input type="text" name="Lname" value="<?php echo sanitize_text($editRecord['Lname'] ?? ''); ?>" required /></label>
                            <label>เบอร์โทร<br /><input type="tel" name="Tel" value="<?php echo sanitize_text($editRecord['Tel'] ?? ''); ?>" required /></label>
                            <label>อีเมล<br /><input type="email" name="Email" value="<?php echo sanitize_text($editRecord['Email'] ?? ''); ?>" required /></label>
                            <label>แบบบ้านที่สนใจ<br /><input type="text" name="ModelInterest" value="<?php echo sanitize_text($editRecord['ModelInterest'] ?? ''); ?>" /></label>
                            <label>งบประมาณ<br /><input type="text" name="PriceInterest" value="<?php echo sanitize_text($editRecord['PriceInterest'] ?? ''); ?>" /></label>
                            <label>เรื่องที่ติดต่อ<br /><input type="text" name="AppointTime" value="<?php echo sanitize_text($editRecord['AppointTime'] ?? ''); ?>" /></label>
                            <label>ช่วงเวลาที่สะดวก<br /><input type="text" name="TimeSlot" value="<?php echo sanitize_text($editRecord['TimeSlot'] ?? ''); ?>" /></label>
                        </div>
                        <label>ความยินยอม<br /><input type="text" name="consent" value="<?php echo sanitize_text($editRecord['consent'] ?? ''); ?>" /></label>
                        <button class="button save" type="submit">บันทึกการแก้ไข</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="notice">ไม่พบข้อมูลที่ต้องการแก้ไข</div>
            <?php endif;
        endif; ?>
    </div>
</body>
</html>
