<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class SP extends REST_Controller
{
    public function index_post()
    {
        $id = $this->post('id');

        $sp = $this->SP_model->getSP($id);

        if ($sp) {
            if ($sp['status_jalan'] == 1) {
                if ($sp['role_id'] == 31) {
                    $posisi = "Sopir";
                } else if ($sp['role_id'] == 32) {
                    $posisi = "Kondektur";
                } else if ($sp['role_id'] == 33) {
                    $posisi = "Kernet";
                }

                if ($sp['mengganti_id'] != 0) {
                    $kru = $this->User_model->get_by_id($sp['mengganti_id']);
                    $nip_nama_mengganti = "[" . $kru['nip'] . "] " . $kru['nama'];
                } else {
                    $nip_nama_mengganti = "-";
                }

                $pengatur = $this->User_model->get_by_id($sp['pengatur_id']);

                if ($pengatur['role_id'] == 1) {
                    $posisi_pengatur = "Pengatur Dinas";
                } else {
                    $posisi_pengatur = "Personalia";
                }

                $tgl_panjang = longdate_indo($sp['tanggal_sp']);
                $tgl_pendek = semi_longdate_indo($sp['tanggal_sp']);

                $data = array(
                    "dinas_id" => $sp['dinas_id'],

                    "nopol" => $sp['nopol'],
                    "mesin" => $sp['mesin'],
                    "kelas" => $sp['kelas'],

                    "kode" => $sp['kode'],
                    "trayek" => $sp['posawal'] . " - " . $sp['posakhir'],
                    "nama" => $sp['nama'],
                    "nip_nama" => "[" . $sp['nip'] . "] " . $sp['nama'],
                    "posisi" => $posisi,
                    "nip_nama_mengganti" => $nip_nama_mengganti,

                    "nama_pengatur" => $pengatur['nama'],
                    "posisi_pengatur" => $posisi_pengatur,
                    "tanggal_panjang" => $tgl_panjang,
                    "tanggal_pendek" => $tgl_pendek
                );
                $this->response([
                    'status' => TRUE,
                    'data' => [$data]
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data tidak ditemukan'
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], REST_Controller::HTTP_OK);
        }
    }
}
