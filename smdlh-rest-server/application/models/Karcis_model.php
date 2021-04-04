<?php

class Karcis_model extends CI_Model
{
    var $table = 't_karcis';

    public function getKarcis($laporan_harian_id = NULL, $posturun = NULL)
    {
        if ($posturun === NULL) {
            $this->db->from($this->table);
            $this->db->where($this->table . '.laporan_harian_id', $laporan_harian_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->from($this->table);
            $this->db->where($this->table . '.laporan_harian_id', $laporan_harian_id);
            $this->db->where($this->table . '.posturun', $posturun);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getJmlPnp($laporan_harian_id, $posturun)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.laporan_harian_id', $laporan_harian_id);
        $this->db->where($this->table . '.posturun', $posturun);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function insertKarcis($data)
    {
        $result = $this->db->insert($this->table, $data);
        return $result;
    }

    public function getLastKarcis($laporan_harian_id)
    {
        $this->db->from($this->table);
        $this->db->where('laporan_harian_id', $laporan_harian_id);
        $this->db->order_by('waktu', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
}
