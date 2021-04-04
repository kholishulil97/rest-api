<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class LaporanKontrol extends REST_Controller
{
    public function index_get()
    {
        $id = $this->get('id');
        $laporan_harian_id = $this->get('laporan_harian_id');
        $cek = $this->get('cek');

        if ($cek === NULL) {
            if ($laporan_harian_id != NULL)
                $laporan_kontrol = $this->LaporanKontrol_model->getLaporanKontrol($id, $laporan_harian_id);
            else
                $laporan_kontrol = $this->LaporanKontrol_model->getLaporanKontrol($id);

            if ($laporan_kontrol) {
                $data = array();
                foreach ($laporan_kontrol as $lk) {
                    $laporan_harian = $this->LaporanHarian_model->get_by_id($lk['laporan_harian_id']);
                    $dinas = $this->Dinas_model->getDinas($laporan_harian['dinas_id']);
                    $user = $this->User_model->get_by_id($lk['petugas_id']);
                    if ($lk['status_turun'] == 0) {
                        $jenis_pelanggaran = "";
                        $keterangan = "";
                        $naik_kontrol = "";
                        $turun_kontrol = "";
                        $tanggal_naik_kontrol = "Kontrol belum selesai";
                        $tanggal_turun_kontrol = "";
                        $jumlah_penumpang = "";
                        $pendapatan_kontrol = "";
                    } else {
                        $jenis_pelanggaran = $lk['jenis_pelanggaran'];
                        $keterangan = $lk['keterangan'];
                        $naik_kontrol = $lk['naik_kontrol'];
                        $turun_kontrol = $lk['turun_kontrol'];
                        $tanggal_naik_kontrol = longdate_indo($lk['tanggal_naik_kontrol']) . " | " . date_format(date_create($lk['tanggal_naik_kontrol']), 'H:i');
                        $tanggal_turun_kontrol = longdate_indo($lk['tanggal_turun_kontrol']) . " | " . date_format(date_create($lk['tanggal_turun_kontrol']), 'H:i');
                        $jumlah_penumpang = $lk['jumlah_penumpang'];
                        $pendapatan_kontrol = "Rp. " . number_format($lk['pendapatan_kontrol'], 2, ",", ".");
                    }
                    $row = array(
                        "id" => $lk['id'],
                        "nopol" => $dinas['nopol'],
                        "trayek" => $dinas['posawal'] . " - " . $dinas['posakhir'],
                        "kelas" => $dinas['kelas'],
                        "nama" => $user['nama'],
                        "laporan_harian_id" => $lk['laporan_harian_id'],
                        "jenis_pelanggaran" => $jenis_pelanggaran,
                        "keterangan" => $keterangan,
                        "naik_kontrol" => $naik_kontrol,
                        "turun_kontrol" => $turun_kontrol,
                        "tanggal_naik_kontrol" => $tanggal_naik_kontrol,
                        "tanggal_turun_kontrol" => $tanggal_turun_kontrol,
                        "jumlah_penumpang" => $jumlah_penumpang,
                        "pendapatan_kontrol" => $pendapatan_kontrol
                    );
                    $data[] = $row;
                }
                $this->response([
                    'status' => TRUE,
                    'data' => $data
                ], REST_Controller::HTTP_OK);
            } else {
                $data = array(
                    "nama" => "Data kontrol tidak ditemukan"
                );
                $this->response([
                    'status' => FALSE,
                    'data' => [$data]
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $laporan_kontrol = $this->LaporanKontrol_model->get_status_by_id($id);

            $data = array(
                'trayek_id' => $laporan_kontrol['laporan_harian_id']
            );

            if ($laporan_kontrol) {
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

    public function index_post()
    {
        $laporan_harian_id = $this->post('laporan_harian_id');
        $petugas_id = $this->post('petugas_id');


        $data = array(
            'laporan_harian_id' => $laporan_harian_id,
            'petugas_id' => $petugas_id,
            'tanggal_naik_kontrol' => date('Y-m-d H:i:s')
        );

        $insert = $this->LaporanKontrol_model->insertLaporanKontrol($data);

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
        $laporan_harian_id = $this->put('laporan_harian_id');
        $petugas_id = $this->put('petugas_id');
        $jenis_pelanggaran = $this->put('jenis_pelanggaran');
        $keterangan = $this->put('keterangan');
        $naik = $this->put('naik');
        $turun = $this->put('turun');
        $jml_pnp = $this->put('jml_pnp');
        $jml_pendapatan = $this->put('jml_pendapatan');

        $data = array(
            'jenis_pelanggaran' => $jenis_pelanggaran,
            'keterangan' => $keterangan,
            'naik_kontrol' => $naik,
            'turun_kontrol' => $turun,
            'jumlah_penumpang' => $jml_pnp,
            'pendapatan_kontrol' => $jml_pendapatan,
            'tanggal_turun_kontrol' => date('Y-m-d H:i:s'),
            'status_turun' => 1
        );

        $insert = $this->LaporanKontrol_model->updateLaporanKontrol(array(
            'laporan_harian_id' => $laporan_harian_id,
            'petugas_id' => $petugas_id,
            'status_turun' => 0
        ), $data);

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
