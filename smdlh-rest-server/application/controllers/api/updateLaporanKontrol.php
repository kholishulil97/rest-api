<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class updateLaporanKontrol extends REST_Controller
{

    public function index_post()
    {
        $laporan_harian_id = $this->post('laporan_harian_id');
        $petugas_id = $this->post('petugas_id');
        $jenis_pelanggaran = $this->post('jenis_pelanggaran');
        $keterangan = $this->post('keterangan');
        $naik = $this->post('naik');
        $turun = $this->post('turun');
        $jml_pnp = $this->post('jml_pnp');
        $jml_pendapatan = $this->post('jml_pendapatan');

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
