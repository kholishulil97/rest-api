<?php

class LaporanKontrol_model extends CI_Model
{
    var $table = 't_laporan_kontrol';

    public function getLaporanKontrol($id = NULL, $laporan_harian_id = NULL)
    {
        if ($id === NULL) {
            $this->db->from($this->table);
            $this->db->where($this->table . '.laporan_harian_id', $laporan_harian_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->from($this->table);
            $this->db->where($this->table . '.petugas_id', $id);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_status_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.petugas_id', $id);
        $this->db->where($this->table . '.status_turun', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insertLaporanKontrol($data)
    {
        $result = $this->db->insert($this->table, $data);
        return $result;
    }

    public function updateLaporanKontrol($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }
}
