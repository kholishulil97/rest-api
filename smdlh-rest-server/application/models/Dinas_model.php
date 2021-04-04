<?php

class Dinas_model extends CI_Model
{
    var $table = 't_dinas';

    public function getDinas($id)
    {
        $this->db->select('`t_dinas`.`id`, `t_dinas`.`bus_id`, `t_dinas`.`trayek_id`, `t_dinas`.`kru_dinas`, `nopol`, `mesin`, `tahun`, `t_bus`.`kelas`, `url`, `status`, `kode`, `posawal`, `posakhir`');
        $this->db->from($this->table);
        $this->db->join('t_bus', 't_bus.id = ' . $this->table . '.bus_id');
        $this->db->join('t_trayek', 't_trayek.id = ' . $this->table . '.trayek_id');
        $this->db->where($this->table . '.id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }
}
