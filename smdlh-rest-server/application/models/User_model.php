<?php

class User_model extends CI_Model
{
    var $table = 't_user';

    public function getUserLogin($nip = null)
    {
        if ($nip === null) {
            return $this->db->get('t_user')->result();
        } else {
            return $this->db->get_where('t_user', [
                'nip' => $nip
            ])->result();
        }
    }



    public function getUser($id = null)
    {
        if ($id === null) {
            $user = $this->db->get('t_user')->row();
        } else {
            $user = $this->db->get_where('t_user', [
                'id' => $id
            ])->row();
        }

        if ($user->dinas_id != 0) {
            $this->db->select('`t_user`.`id`,`nip`,`nama`,`fotoprofil`,`role_id`, `t_bus`.`nopol`, `t_bus`.`kelas`, `t_bus`.`url`, `t_trayek`.`posawal`, `t_trayek`.`posakhir`');
            $this->db->from($this->table);
            $this->db->join('t_dinas', $this->table . '.dinas_id = t_dinas.id');
            $this->db->join('t_bus', 't_dinas.bus_id = t_bus.id');
            $this->db->join('t_trayek', 't_dinas.trayek_id = t_trayek.id');
            $this->db->where($this->table . '.id', $id);
            $query = $this->db->get();
            return $query->result();
        } else {
            $data = [array(
                "id" => $user->id,
                "nip" => $user->nip,
                "nama" => $user->nama,
                "fotoprofil" => $user->fotoprofil,
                "role_id" => $user->role_id,
                "nopol" => "Belum Tersedia",
                "kelas" => "",
                "url" => "",
                "posawal" => "",
                "posakhir" => ""
            )];
            return $data;
        }
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }

    public function get_by_dinas_id($dinas_id = NULL, $role_id = NULL)
    {
        if ($role_id === NULL) {
            $this->db->from($this->table);
            $this->db->where('dinas_id', $dinas_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->from($this->table);
            $this->db->where('dinas_id', $dinas_id);
            $this->db->where('role_id', $role_id);
            $query = $this->db->get();
            return $query->row_array();
        }
    }
}
