<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มผู้ป่วย</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/patient_create.css'); ?>">
</head>
<body>
<main>
    <a href="<?php echo site_url('patient'); ?>">← กลับหน้ารายชื่อ</a>
    <h1>เพิ่มผู้ป่วย</h1>
    <p>กรอกข้อมูลให้ครบทุกช่อง โดยใช้ปีเกิด ค.ศ.</p>
    <?php if ($save_error): ?>
        <p class="notice error" role="alert"><?php echo html_escape($save_error); ?></p>
    <?php endif; ?>
    <!-- form_open สร้าง hidden input สำหรับ CSRF token ให้อัตโนมัติ -->
    <?php echo form_open('patient/create'); ?>
        <div class="field">
            <label for="hn_number">เลข HN</label>
            <input id="hn_number" name="hn_number" type="text" maxlength="20" required
                   value="<?php echo set_value('hn_number'); ?>" aria-describedby="hn-error">
            <div id="hn-error" class="error"><?php echo form_error('hn_number'); ?></div>
        </div>
        <div class="field">
            <label for="name">ชื่อ-นามสกุล</label>
            <input id="name" name="name" type="text" maxlength="100" required
                   value="<?php echo set_value('name'); ?>" aria-describedby="name-error">
            <div id="name-error" class="error"><?php echo form_error('name'); ?></div>
        </div>
        <div class="field">
            <label for="dob">วันเกิด (ค.ศ.)</label>
            <input id="dob" name="dob" type="date" min="1000-01-01" max="<?php echo html_escape($today); ?>" required
                   value="<?php echo set_value('dob'); ?>" aria-describedby="dob-error">
            <div id="dob-error" class="error"><?php echo form_error('dob'); ?></div>
        </div>
        <div class="actions">
            <button type="submit">บันทึกผู้ป่วย</button>
            <a href="<?php echo site_url('patient'); ?>">ยกเลิก</a>
        </div>
    <?php echo form_close(); ?>
</main>
</body>
</html>
