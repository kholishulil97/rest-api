<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Karcis extends REST_Controller
{
    public function index_get()
    {
        $id = $this->get('id');
        $laporan_harian_id = $this->get('laporan_harian_id');
        $posturun = $this->get('posturun', true);

        if ($posturun != NULL) {
            $karcis = $this->Karcis_model->getKarcis($laporan_harian_id, $posturun);
            $jml = $this->Karcis_model->getJmlPnp($laporan_harian_id, $posturun);
            $data = array();
            foreach ($karcis as $k) {
                $row = array(
                    "id" => $k['id'],
                    "laporan_harian_id" =>  $k['laporan_harian_id'],
                    "waktu" => longdate_indo($k['waktu']) . " | " . date_format(date_create($k['waktu']), 'H:i'),
                    "posnaik" => $k['posnaik'],
                    "posturun" => $k['posturun'],
                    "tarif" => "Rp. " . number_format($k['tarif'], 2, ",", ".")
                );
                $data[] = $row;
            }

            if ($data) {
                $this->response([
                    'status' => TRUE,
                    'jumlah_penumpang' => strval($jml),
                    'data' => $data
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data tidak ditemukan'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $laporan_harian = $this->LaporanHarian_model->get_by_id($laporan_harian_id);

            $user = $this->User_model->get_by_dinas_id($laporan_harian['dinas_id'], 32);

            $dinas = $this->Dinas_model->getDinas($laporan_harian['dinas_id']);
            if ($dinas) {
                $pos = $this->Tarif_model->getPos($dinas['trayek_id']);
                $array_pos = array();
                foreach ($pos as $p) {
                    array_push($array_pos, $p['posnaik']);
                }
                if ($laporan_harian['status_selesai'] == 0) {
                    $data = array(
                        "trayek_id" => $dinas['trayek_id'],
                        'kondektur' => "[" . $user['nip'] . "] " . $user['nama'],
                        'bus' => $dinas['nopol'] . " - " . $dinas['kelas'],
                        'kode' => $dinas['kode'],
                        "posnaik" => $array_pos
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
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data tidak ditemukan'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function index_post()
    {
        $laporan_harian_id = $this->post('laporan_harian_id');
        $posnaik = $this->post('posnaik');
        $posturun = $this->post('posturun');
        $tarif = $this->post('tarif');

        $data = array(
            'laporan_harian_id'  => $laporan_harian_id,
            'waktu' => date('Y-m-d H:i:s'),
            'posnaik'  => $posnaik,
            'posturun'  => $posturun,
            'tarif'  => $tarif
        );

        $insert = $this->Karcis_model->insertKarcis($data);

        $id_last = $this->Karcis_model->getLastKarcis($laporan_harian_id);

        if ($insert) {
            $this->response([
                'status' => TRUE,
                'id_karcis' => $id_last['id'],
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
