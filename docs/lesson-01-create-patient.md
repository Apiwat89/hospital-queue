# บทที่ 1: เพิ่มผู้ป่วยด้วย CI3

เปิด http://localhost/hospital-queue/index.php/patient แล้วกดเพิ่มผู้ป่วย

## อ่านโค้ดตามลำดับนี้

1. application/views/patient_create.php: form_open('patient/create') สร้างฟอร์ม POST และ CSRF token
2. application/controllers/Patient.php: create() แยก GET (แสดงฟอร์ม) กับ POST (รับข้อมูล)
3. Controller รับเฉพาะ hn_number, name, dob แล้วตรวจด้วย Form Validation
4. ถ้าไม่ผ่าน จะกลับมาแสดง View เดิม; set_value() คืนค่าที่กรอกและ escape สำหรับ HTML ส่วน form_error() แสดงข้อผิดพลาดแต่ละช่อง
5. ถ้าผ่าน จะเรียก Patient_model->create($patient) เพื่อ INSERT ผ่าน Query Builder
6. บันทึกสำเร็จแล้ว redirect HTTP 303 ไปหน้ารายชื่อ (Post/Redirect/Get) เพื่อไม่ให้ refresh หน้าผลลัพธ์ส่ง POST เดิมซ้ำ

## ทำไมต้องตรวจสองชั้น

HTML required, maxlength และ max ช่วยผู้ใช้กรอกง่ายขึ้น แต่ผู้ใช้สามารถส่ง request ข้ามข้อกำหนดเหล่านี้ได้ จึงต้องตรวจซ้ำฝั่ง PHP เสมอ

is_unique ช่วยแจ้ง HN ซ้ำก่อนบันทึก ส่วน UNIQUE ใน MySQL ป้องกันข้อมูลซ้ำแม้มีสองคำขอบันทึกพร้อมกัน Model จัดการ duplicate error อีกครั้ง

ได้เพิ่ม UNIQUE ในฐานข้อมูล hospital_db ของเครื่องนี้แล้ว ไม่มีการลบข้อมูลเดิม:
ALTER TABLE patients ADD UNIQUE KEY uq_patients_hn_number (hn_number);

หากย้ายฐานข้อมูลไปเครื่องอื่น ให้ตรวจว่ามี index นี้หรือยังก่อนรัน ห้ามรันซ้ำบนเครื่องปัจจุบัน และหากข้อมูล HN ซ้ำต้องตรวจสอบข้อมูลก่อนเพิ่ม index

## CSS

- assets/css/patient_list.css: หน้ารายชื่อและค้นหา
- assets/css/patient_create.css: ฟอร์มเพิ่มผู้ป่วย

## สิ่งที่ควรเข้าใจ

- required: ห้ามว่าง
- max_length: จำกัดความยาวตามคอลัมน์ในฐานข้อมูล
- callback_valid_dob: ตรวจวันที่จริงและไม่เกินวันนี้ โดยใช้เวลาไทย
- html_escape: ทำให้ข้อมูลแสดงเป็นข้อความ ไม่ถูกตีความเป็น HTML
- CSRF: เปิดใน application/config/config.php และใช้ form_open เพื่อแนบ token
- created=1: ใช้แสดงข้อความสำเร็จหลัง redirect เท่านั้น ไม่ใช่หลักฐานว่ามีการบันทึก ผู้ใช้แก้ URL นี้เองได้
- Redirect ลดการส่งซ้ำจากการ refresh แต่ยังต้องมี UNIQUE ป้องกัน HN ซ้ำ

## ลองด้วยตัวเอง

ใช้ข้อมูลสมมติ:
1. เพิ่ม HN LEARN001 ชื่อ ทดสอบ เรียนรู้ วันเกิด 2000-01-15
2. ค้นหา LEARN001 จากหน้ารายชื่อ
3. เพิ่ม HN เดิม ต้องเห็นข้อความ HN ซ้ำ และค่าที่กรอกยังอยู่
4. ทดลองเว้นชื่อว่าง / เลือกวันเกิดในอนาคต
5. หลังบันทึกสำเร็จ refresh หน้ารายชื่อ ต้องไม่มีข้อมูลเพิ่มซ้ำ

ลองตอบก่อนเริ่มบทต่อไป:
- name="hn_number" ใน HTML เกี่ยวข้องกับ $this->input->post('hn_number') อย่างไร?
- ทำไมตรวจ required ใน HTML แล้วต้องตรวจใน PHP อีก?
- ทำไม Model ไม่เป็นคนสร้าง HTML?
- ถ้าบันทึกแล้วแสดง View ทันทีโดยไม่ redirect จะเกิดอะไรเมื่อ refresh?

บทถัดไป: แก้ไขผู้ป่วย โดยค้นหาด้วย ID และตรวจ HN ซ้ำโดยยกเว้นผู้ป่วยคนเดิม
