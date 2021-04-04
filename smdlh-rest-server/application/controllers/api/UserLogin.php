<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class UserLogin extends REST_Controller
{
    public function index_get()
    {
        $nip = $this->get('nip');
        $password = $this->get('password');

        if ($nip === NULL) {
            $user = $this->User_model->getUser();
        } else {
            $user = $this->User_model->getUser($nip, $password);
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

    public function index_post()
    {
        $nip = $this->post('nip');
        $password = $this->post('password');

        if ($nip === NULL) {
            $user = $this->User_model->getUserLogin();
        } else {
            $user = $this->User_model->getUserLogin($nip);
        }


        if ($user) {
            //jika usernya ada
            //cek password
            foreach ($user as $u) {
                if ($u->role_id == 31 || $u->role_id == 32 || $u->role_id == 33 || $u->role_id == 4) {
                    //jika role_id sesuai
                    if ($u->password == $password) {
                        //jika password inputan sama dengan di database
                        //mengembalikan json
                        $this->response([
                            'status' => TRUE,
                            'data' => [$u]
                        ], REST_Controller::HTTP_OK);
                    } else {
                        //jika password tidak sama
                        $this->response([
                            'status' => FALSE,
                            'message' => 'Password salah!'
                        ], REST_Controller::HTTP_OK);
                    }
                } else {
                    //jika role_id tidak sesuai
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Akses jabatan dilarang!'
                    ], REST_Controller::HTTP_OK);
                }
            }
        } else {
            //jika tidak ada NIP yang cocok
            $this->response([
                'status' => FALSE,
                'message' => 'NIP tidak terdaftar!'
            ], REST_Controller::HTTP_OK);
        }
    }
}
