<?php

class LaporanHarian_model extends CI_Model
{
    var $table = 't_laporan_harian';

    public function getLaporanHarian($id = NULL, $status_setor = NULL)
    {
        if ($status_setor === NULL) {
            $this->db->from($this->table);
            $this->db->where('status_selesai', 0);
            $this->db->order_by('tanggal_jalan', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->from($this->table);
            $this->db->where($this->table . '.dinas_id', $id);
            $this->db->where($this->table . '.status_setor', $status_setor);
            $this->db->order_by('tanggal_selesai', 'DESC');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insertLaporanHarian($data)
    {
        $result = $this->db->insert($this->table, $data);
        return $result;
    }

    public function updateLaporanHarian($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }
}
