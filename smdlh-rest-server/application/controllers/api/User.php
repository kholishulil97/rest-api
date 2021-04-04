<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class User extends REST_Controller
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

        if ($id === NULL) {
            $user = $this->User_model->getUser();
        } else {
            $user = $this->User_model->getUser($id);
        }


        if ($user) {
            $this->response([
                'status' => TRUE,
                'data' => $user
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
