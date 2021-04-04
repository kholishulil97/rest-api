<?php

class Tarif_model extends CI_Model
{
    var $table = 't_tarif';

    public function getTarif($trayek_id)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.trayek_id', $trayek_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getPos($trayek_id)
    {
        $this->db->distinct();
        $this->db->select('posnaik');
        $this->db->from($this->table);
        $this->db->where($this->table . '.trayek_id', $trayek_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getNominal($trayek_id, $posnaik, $posturun)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.trayek_id', $trayek_id);
        $this->db->where($this->table . '.posnaik', $posnaik);
        $this->db->where($this->table . '.posturun', $posturun);
        $query = $this->db->get();
        return $query->row_array();
    }
}
