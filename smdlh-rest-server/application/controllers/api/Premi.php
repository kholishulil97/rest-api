<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Premi extends REST_Controller
{
    public function index_get()
    {
        $nip = $this->get('nip');
        $password = $this->get('password');

        if ($nip === NULL) {
            $user = $this->User_model->getUserLogin();
        } else {
            $user = $this->User_model->getUserLogin($nip);
        }
        foreach ($user as $u) {
            if ($u->password == $password) {
                $this->response([
                    'status' => TRUE,
                    'message' => "Password benar"
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data tidak ditemukan'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function index_post()
    {
        $id = $this->post('id');
        $status_ambil = $this->post('status_ambil');


        $data = array();

        $premi = $this->Premi_model->get_by_kru_id($id, $status_ambil);
        $bulan = $this->Premi_model->get_bulan($id, $status_ambil);

        foreach ($bulan as $b) {
            $nominal = 0;
            foreach ($premi as $p) {
                $waktu_setor = new DateTime($p->tanggal_setor);
                $t = $waktu_setor->format('Y-m');
                if ($t == $b["DATE_FORMAT(`t_laporan_harian`.`tanggal_setor`, '%Y-%m')"]) {
                    $nominal += $p->nominal_premi;
                    $dinas_id = $p->dinas_id;
                    $kasir_id = $p->kasir_id;
                    $tanggal_ambil = $p->tanggal_ambil;
                } else {
                    continue;
                }
            }
            $dinas = $this->Dinas_model->getDinas($dinas_id);
            $nopol = $dinas['nopol'];
            $trayek = $dinas['posawal'] . " - " . $dinas['posakhir'];
            $kelas = $dinas['kelas'];

            $kasir = $this->User_model->get_by_id($kasir_id);

            $nipnamakasir = $kasir['nama'] . " - " . $kasir['nip'];

            if ($status_ambil == 0) {
                $row = array(
                    "bulan_setor" => cust_date_indo($b["DATE_FORMAT(`t_laporan_harian`.`tanggal_setor`, '%Y-%m')"]),
                    "nopol" => $nopol,
                    "trayek" => $trayek,
                    "kelas" => $kelas,
                    "nominal" => "Rp. " .  number_format($nominal, 2, ",", "."),
                    "kasir" => "",
                    "tanggal_ambil" => "",
                    "jam_ambil" => "",
                    "status_ambil" => "Belum Diambil"
                );
                $data[] = $row;
            } else {
                $row = array(
                    "bulan_setor" => cust_date_indo($b["DATE_FORMAT(`t_laporan_harian`.`tanggal_setor`, '%Y-%m')"]),
                    "nopol" => $nopol,
                    "trayek" => $trayek,
                    "kelas" => $kelas,
                    "nominal" => "Rp. " . number_format($nominal, 2, ",", "."),
                    "kasir" => $nipnamakasir,
                    "tanggal_ambil" => longdate_indo($tanggal_ambil),
                    "jam_ambil" => date_format(date_create($tanggal_ambil), 'H:i'),
                    "status_ambil" => "Sudah Diambil"
                );
                $data[] = $row;
            }
        }



        if ($data) {
            $this->response([
                'status' => TRUE,
                'data' => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
