<?php

class Premi_model extends CI_Model
{
    var $table = 't_bayar_premi';

    public function getPremi($id)
    {
        $this->db->from($this->table);
        $this->db->where($this->table . '.laporan_harian_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_id_kru()
    {
        $this->db->distinct();
        $this->db->select('kru_id');
        $this->db->from($this->table);
        $query = $this->db->get();

        return $query->result();
    }

    public function get_by_kru_id($kru_id, $status_ambil)
    {
        $this->db->select('`t_laporan_harian`.`status_setor`,`t_laporan_harian`.`dinas_id`,`t_laporan_harian`.`tanggal_setor`,`t_bayar_premi`.`id`,`t_bayar_premi`.`kru_id`,`t_bayar_premi`.`laporan_harian_id`,`t_bayar_premi`.`status_ambil`,`t_bayar_premi`.`nominal_premi`,`t_bayar_premi`.`tanggal_ambil`,`t_bayar_premi`.`kasir_id`');
        $this->db->from($this->table);
        $this->db->join('`t_laporan_harian`', '`t_laporan_harian`.`id` = `t_bayar_premi`.`laporan_harian_id`');
        $this->db->where('kru_id', $kru_id);
        $this->db->where('status_ambil', $status_ambil);
        $query = $this->db->get();

        return $query->result();
    }

    public function get_bulan($kru_id, $status_ambil)
    {
        $this->db->distinct();
        $this->db->select('DATE_FORMAT(`t_laporan_harian`.`tanggal_setor`,\'%Y-%m\')');
        $this->db->join('`t_laporan_harian`', '`t_laporan_harian`.`id` = `t_bayar_premi`.`laporan_harian_id`');
        $this->db->from($this->table);
        $this->db->where('`t_bayar_premi`.`status_ambil`', $status_ambil);
        $this->db->where('`t_bayar_premi`.`kru_id`', $kru_id);
        $query = $this->db->get();

        return $query->result_array();
    }
}
