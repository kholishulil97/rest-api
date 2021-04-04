<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class updatePengeluaran extends REST_Controller
{
    public function index_post()
    {
        $data = array(
            'nama_pengeluaran'  => $this->post('nama_pengeluaran'),
            'lokasi' => $this->post('lokasi'),
            'nominal' => $this->post('nominal'),
            'keterangan' => $this->post('keterangan')
        );

        $insert = $this->Pengeluaran_model->updatePengeluaran(array('id' => $this->post('id')), $data);

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
