<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Pengeluaran extends REST_Controller
{


    public function index_get($laporan_harian_id)
    {

        $pengeluaran = $this->Pengeluaran_model->getPengeluaran($laporan_harian_id);
        $data = array();
        foreach ($pengeluaran as $p) {
            $row = array(
                'id'  => $p['id'],
                'laporan_harian_id'  => $p['laporan_harian_id'],
                "nama_pengeluaran" => $p['nama_pengeluaran'],
                "lokasi" => $p['lokasi'],
                "nominal" => "Rp. " . number_format($p['nominal'], 2, ",", "."),
                "angka" => $p['nominal'],
                "tanggal" => longdate_indo($p['waktu']),
                "waktu" => date_format(date_create($p['waktu']), 'H:i'),
                "keterangan" => $p['keterangan']
            );
            $data[] = $row;
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

    public function index_post()
    {

        $data = array(
            'laporan_harian_id'  => $this->post('laporan_harian_id'),
            'nama_pengeluaran'  => $this->post('nama_pengeluaran'),
            'lokasi' => $this->post('lokasi'),
            'nominal' => $this->post('nominal'),
            'waktu' => date('Y-m-d H:i:s'),
            'keterangan' => $this->post('keterangan')
        );

        $insert = $this->Pengeluaran_model->insertPengeluaran($data);

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
            'nama_pengeluaran'  => $this->put('nama_pengeluaran'),
            'lokasi' => $this->put('lokasi'),
            'nominal' => $this->put('nominal'),
            'keterangan' => $this->put('keterangan')
        );

        $insert = $this->Pengeluaran_model->updatePengeluaran(array('id' => $this->put('id')), $data);

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

    public function index_delete($id)
    {

        $delete = $this->Pengeluaran_model->deletePengeluaran($id);

        if ($delete) {
            $data = array('id' => $id);
            $this->response([
                'status' => TRUE,
                'data' => [$data]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data gagal dihapus!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
