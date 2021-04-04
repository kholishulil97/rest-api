<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class hapusPengeluaran extends REST_Controller
{

    public function index_get($id)
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
