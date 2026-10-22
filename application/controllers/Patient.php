<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Patient_model');
        $this->load->library('pagination');
    }

    public function index() { 
        // 1. รับค่าคำค้นหา (ถ้ายูสเซอร์พิมพ์มา)
        $keyword = $this->input->get('q');
        $keyword = is_string($keyword) ? trim($keyword) : '';

        // 2. ตั้งค่า Pagination
        $config['base_url'] = base_url('index.php/patient/index'); 
        
        // *แก้ไข: ส่ง Keyword ไปให้ Model เพื่อนับจำนวนเฉพาะ "คนที่ค้นเจอ"
        $config['total_rows'] = $this->Patient_model->get_count($keyword); 
        $config['per_page'] = 5; 

        // *จุดสำคัญ! บอก Pagination ให้พ่วง Query String (?q=...) ไปกับปุ่ม 1 2 3 ด้วย
        $config['reuse_query_string'] = TRUE; 
        
        $this->pagination->initialize($config);

        $page = max(0, (int) $this->uri->segment(3));

        // 3. สั่ง Model ดึงข้อมูล โดยส่ง Keyword เข้าไปด้วย
        $data['patients'] = $this->Patient_model->get_patients($config['per_page'], $page, $keyword);
        
        $data['links'] = $this->pagination->create_links();
        $data['keyword'] = $keyword;
        $data['created'] = $this->input->get('created') === '1';
        
        $this->load->view('patient_list', $data);
    }

    // GET แสดงฟอร์ม / POST ตรวจข้อมูลและบันทึก
    public function create() {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['save_error'] = '';

        if ($this->input->method() === 'post') {
            // รับเฉพาะช่องที่อนุญาต และป้องกันการส่ง array แทนข้อความ
            $patient = array();
            foreach (array('hn_number', 'name', 'dob') as $field) {
                $value = $this->input->post($field);
                $patient[$field] = is_string($value) ? trim($value) : '';
            }
            $this->form_validation->set_data($patient);
            $this->form_validation->set_rules('hn_number', 'HN', 'required|max_length[20]|is_unique[patients.hn_number]');
            $this->form_validation->set_rules('name', 'ชื่อ-นามสกุล', 'required|max_length[100]');
            $this->form_validation->set_rules('dob', 'วันเกิด', 'required|callback_valid_dob');
            $this->form_validation->set_message('required', 'กรุณากรอก{field}');
            $this->form_validation->set_message('max_length', '{field}ต้องไม่เกิน {param} ตัวอักษร');
            $this->form_validation->set_message('is_unique', 'HN นี้มีอยู่แล้ว กรุณาตรวจสอบอีกครั้ง');

            if ($this->form_validation->run()) {
                $result = $this->Patient_model->create($patient);
                if ($result === TRUE) {
                    // Redirect หลัง POST เพื่อไม่ให้ refresh แล้วส่งฟอร์มซ้ำ
                    redirect('patient/index?created=1', 'location', 303);
                    return;
                }
                $data['save_error'] = $result === 'duplicate'
                    ? 'HN นี้มีอยู่แล้ว กรุณาตรวจสอบอีกครั้ง'
                    : 'บันทึกไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
            }
        }
        $data['today'] = (new DateTimeImmutable('now', new DateTimeZone('Asia/Bangkok')))->format('Y-m-d');
        $this->load->view('patient_create', $data);
    }

    public function valid_dob($value) {
        $timezone = new DateTimeZone('Asia/Bangkok');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, $timezone);
        $today = new DateTimeImmutable('today', $timezone);
        if (!$date || $date->format('Y-m-d') !== $value || $value < '1000-01-01' || $date > $today) {
            $this->form_validation->set_message('valid_dob', 'วันเกิดต้องเป็นวันที่ถูกต้องและไม่อยู่ในอนาคต (ปี ค.ศ.)');
            return FALSE;
        }
        return TRUE;
    }
}
