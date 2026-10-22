<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายชื่อผู้ป่วย</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/patient_list.css'); ?>">
</head>
<body>
<main>
    <header>
        <h1>รายชื่อผู้ป่วย</h1>
        <a class="button" href="<?php echo site_url('patient/create'); ?>">+ เพิ่มผู้ป่วย</a>
    </header>
    <?php if ($created): ?>
        <p class="notice" role="status">บันทึกผู้ป่วยสำเร็จ</p>
    <?php endif; ?>
    <form class="search-form" action="<?php echo site_url('patient/index'); ?>" method="get">
        <label for="q">ค้นหาผู้ป่วย</label>
        <input id="q" type="search" name="q" placeholder="HN หรือชื่อผู้ป่วย"
               value="<?php echo html_escape($keyword); ?>">
        <button type="submit">ค้นหา</button>
        <a href="<?php echo site_url('patient'); ?>">ล้างค่า</a>
    </form>
    <div class="table-wrapper">
        <table>
            <thead><tr><th scope="col">ID</th><th scope="col">HN</th><th scope="col">ชื่อ-นามสกุล</th><th scope="col">วันเกิด</th></tr></thead>
            <tbody>
            <?php if (empty($patients)): ?>
                <tr><td colspan="4" class="empty">ไม่พบข้อมูลผู้ป่วย</td></tr>
            <?php else: ?>
                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?php echo (int) $p['id']; ?></td>
                    <td><?php echo html_escape($p['hn_number']); ?></td>
                    <td><?php echo html_escape($p['name']); ?></td>
                    <td><?php echo html_escape($p['dob']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination" aria-label="หน้ารายชื่อผู้ป่วย"><?php echo $links; ?></nav>
</main>
</body>
</html>
