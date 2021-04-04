<?php

class Pengeluaran_model extends CI_Model
{
    var $table = 't_pengeluaran';

    public function getPengeluaran($id)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.laporan_harian_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insertPengeluaran($data)
    {
        $result = $this->db->insert($this->table, $data);
        return $result;
    }

    public function updatePengeluaran($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function deletePengeluaran($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }
}
