<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class updateLaporanHarian extends REST_Controller
{

    public function index_post()
    {
        $data = array(
            'status_selesai'  => 1,
            'tanggal_selesai' => date('Y-m-d H:i:s')
        );

        $insert = $this->LaporanHarian_model->updateLaporanHarian(array('id' => $this->post('id')), $data);

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
