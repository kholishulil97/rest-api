<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class LaporanHarian extends REST_Controller
{
    public function index_get()
    {
        $id = $this->get('id');
        $status = $this->get('status');

        $user = $this->User_model->get_by_id($id);

        if ($user) {
            if ($status === NULL) {
                $laporan_array = $this->LaporanHarian_model->getLaporanHarian($user['dinas_id']);
                if ($laporan_array) {
                    $data = array(
                        "laporan_harian_id" => $laporan_array['id'],
                        "nominal" => "",
                        "tanggal" => "",
                        "jam_selesai" => "",
                        "nopol" => "",
                        "trayek" => "",
                        "kelas" => "",
                        "status_setor_kata" => "",
                        "nip_nama_kasir" => "",
                        "tanggal_setor_long" => ""
                    );
                    $this->response([
                        'status' => TRUE,
                        'data' => [$data]
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Data tidak ditemukan'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $laporan_array = $this->LaporanHarian_model->getLaporanHarian($user['dinas_id'], $status);
                if ($laporan_array) {
                    if ($user['dinas_id'] != 0) {
                        $data = array();
                        foreach ($laporan_array as $lp) {
                            if ($lp['status_selesai'] == 0) {
                                $tanggal = "Dinas belum selesai";
                                $jam_selesai = "";
                            } else {
                                $tanggal = longdate_indo($lp['tanggal_selesai']);
                                $jam_selesai = date_format(date_create($lp['tanggal_selesai']), 'H:i');
                            }
                            $laporan = array();
                            $pemasukan = 0;
                            $minus = 0;
                            $nominal = 0;
                            $karcis = $this->Karcis_model->getKarcis($lp['id']);
                            foreach ($karcis as $k) {
                                $pemasukan += intval($k['tarif']);
                            }
                            $pengeluaran = $this->Pengeluaran_model->getPengeluaran($lp['id']);
                            foreach ($pengeluaran as $p) {
                                $minus += intval($p['nominal']);
                            }
                            $nominal = ($pemasukan - $minus) * 91 / 100;
                            $nominal = "Rp. " . number_format($nominal, 2, ",", ".");

                            $dinas = $this->Dinas_model->getDinas($lp['dinas_id']);
                            $nopol = $dinas['nopol'];
                            $posawal = $dinas['posawal'];
                            $posakhir = $dinas['posakhir'];
                            $kelas = $dinas['kelas'];
                            if ($lp['status_setor'] == 0) {
                                $status_setor_kata = "Belum Disetor";
                                $nip_nama_kasir = "";
                                $tanggal_setor_long = "";
                            } else {
                                $status_setor_kata = "Sudah Disetor";
                                $kasir = $this->User_model->get_by_id($lp['kasir_id']);
                                $nip_nama_kasir = "[" . $kasir['nip'] . "] " . $kasir['nama'];
                                $tanggal_setor_long = longdate_indo($lp['tanggal_setor']) . " | " . date_format(date_create($lp['tanggal_setor']), 'H:i');
                            }
                            $laporan = array(
                                "laporan_harian_id" => $lp['id'],
                                "nominal" => $nominal,
                                "tanggal" => $tanggal,
                                "jam_selesai" => $jam_selesai,
                                "nopol" => $nopol,
                                "trayek" => $posawal . " - " . $posakhir,
                                "kelas" => $kelas,
                                "status_setor_kata" => $status_setor_kata,
                                "nip_nama_kasir" => $nip_nama_kasir,
                                "tanggal_setor_long" => $tanggal_setor_long
                            );
                            $data[] = $laporan;
                        }
                        $this->response([
                            'status' => TRUE,
                            'data' => $data
                        ], REST_Controller::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Data tidak ditemukan'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_post()
    {
        $dinas_id = $this->post('dinas_id');

        $data = array(
            'dinas_id'  => $dinas_id,
            'tanggal_jalan' => date('Y-m-d H:i:s')
        );

        $insert = $this->LaporanHarian_model->insertLaporanHarian($data);

        if ($insert) {
            $this->response([
                'status' => TRUE,
                'data' => [$data]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data gagal disimpan!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put()
    {

        $data = array(
            'status_selesai'  => 1,
            'tanggal_selesai' => date('Y-m-d H:i:s')
        );

        $insert = $this->LaporanHarian_model->updateLaporanHarian(array('id' => $this->put('id')), $data);

        if ($insert) {
            $this->response([
                'status' => TRUE,
                'data' => [$data]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data gagal disimpan!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
