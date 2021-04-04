<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Invoice extends REST_Controller
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
        $trayek_id = $this->post('trayek_id');
        $posnaik = $this->post('posnaik');
        $posturun = $this->post('posturun');
        $jml_pnp = $this->post('jml_pnp');


        $nominal = $this->Tarif_model->getNominal($trayek_id, $posnaik, $posturun);



        if ($nominal) {
            $harga = intval($nominal['tarif']) * $jml_pnp;

            $lima = $this->roundUpToAny($harga, 5000);
            $duapuluh = $this->roundUpToAny($harga, 20000);
            $limapuluh = $this->roundUpToAny($harga, 50000);
            $seratus = $this->roundUpToAny($harga, 100000);

            $nominal = array(
                "harga_bayar" => $harga,
                "lima" => $lima,
                "duapuluh" => $duapuluh,
                "limapuluh" => $limapuluh,
                "seratus" => $seratus,
                "harga_bayar_format" => number_format($harga, 0, ",", "."),
                "lima_format" => "Rp. " . number_format($lima, 0, ",", "."),
                "duapuluh_format" => "Rp. " . number_format($duapuluh, 0, ",", "."),
                "limapuluh_format" => "Rp. " . number_format($limapuluh, 0, ",", "."),
                "seratus_format" => "Rp. " . number_format($seratus, 0, ",", ".")
            );

            $this->response([
                'status' => TRUE,
                'data' => [$nominal]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    function roundUpToAny($n, $x)
    {
        return (ceil($n) % $x === 0) ? ceil($n) : round(($n + $x / 2) / $x) * $x;
    }
}
