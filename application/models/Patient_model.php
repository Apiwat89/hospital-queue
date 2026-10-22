<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient_model extends CI_Model {
    
    // 1. หาจำนวนผู้ป่วยทั้งหมดในระบบ (เพื่อเอาไปคำนวณจำนวนหน้า)
    public function get_count($keyword = null) {
        if ($keyword !== null && $keyword !== '') {
            $this->db->group_start(); // เปิดวงเล็บกลุ่ม
            $this->db->like('hn_number', $keyword);
            $this->db->or_like('name', $keyword);
            $this->db->group_end(); // ปิดวงเล็บกลุ่ม
        }
        return $this->db->count_all_results('patients');
    }

    // 2. ดึงข้อมูลแบบมี Limit และ Offset
    public function get_patients($limit, $start, $keyword = null) {
        if ($keyword !== null && $keyword !== '') {
            $this->db->group_start(); // เปิดวงเล็บกลุ่ม
            $this->db->like('hn_number', $keyword);
            $this->db->or_like('name', $keyword);
            $this->db->group_end(); // ปิดวงเล็บกลุ่ม
        }
        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit, $start);
        $query = $this->db->get('patients');
        return $query->result_array();
    }

    public function create($patient) {
        // ปิดหน้ารายละเอียด SQL error ชั่วคราว เพื่อคืนข้อความที่เหมาะสมให้ฟอร์ม
        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $saved = $this->db->insert('patients', $patient);
        $error = $this->db->error();
        $this->db->db_debug = $debug;
        if ($saved) {
            return TRUE;
        }
        if ((int) $error['code'] === 1062) {
            return 'duplicate';
        }
        log_message('error', 'Patient insert failed; database error code: '.(int) $error['code']);
        return FALSE;
    }
}
