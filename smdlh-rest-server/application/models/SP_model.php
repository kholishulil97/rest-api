<?php

class SP_model extends CI_Model
{
    var $table = 't_SP';

    public function getSP($id)
    {
        $this->db->select('t_sp.id, t_sp.dinas_id, t_sp.kru_id, t_sp.mengganti_id, t_sp.nomor_sp, t_sp.tanggal_sp, t_sp.pengatur_id, t_dinas.bus_id, t_dinas.trayek_id, t_dinas.kru_dinas, t_dinas.status_jalan, t_bus.nopol, t_bus.mesin, t_bus.kelas, t_bus.status, t_trayek.kode, t_trayek.posawal, t_trayek.posakhir, t_user.nama, t_user.nip, t_user.role_id');
        $this->db->from($this->table);
        $this->db->join('t_dinas', 't_dinas.id = ' . $this->table . '.dinas_id');
        $this->db->join('t_user', 't_user.id = ' . $this->table . '.kru_id');
        $this->db->join('t_bus', 't_dinas.bus_id = t_bus.id');
        $this->db->join('t_trayek', 't_dinas.trayek_id = t_trayek.id');
        $this->db->where('t_sp.kru_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
}
