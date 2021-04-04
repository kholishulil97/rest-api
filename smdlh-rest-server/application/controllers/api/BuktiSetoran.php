<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class BuktiSetoran extends REST_Controller
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
        $laporan_harian_id = $this->post('laporan_harian_id');
        $pemasukan = 0;
        $pengeluaran = 0;
        $nip_nama_kernet = "-";
        $nama_kernet = "-";
        $premi_kernet = 0;

        function penyebut($nilai)
        {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
                $temp = " " . $huruf[$nilai];
            } else if ($nilai < 20) {
                $temp = penyebut($nilai - 10) . " belas";
            } else if ($nilai < 100) {
                $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
            } else if ($nilai < 1000000000) {
                $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
                $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
            } else if ($nilai < 1000000000000000) {
                $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
            }
            return $temp;
        }

        function terbilang($nilai)
        {
            if ($nilai < 0) {
                $hasil = "minus " . trim(penyebut($nilai));
            } else {
                $hasil = trim(penyebut($nilai));
            }
            return $hasil;
        }

        $pemasukan_array = $this->Karcis_model->getKarcis($laporan_harian_id);
        $pengeluaran_array = $this->Pengeluaran_model->getPengeluaran($laporan_harian_id);
        $laporanharian = $this->LaporanHarian_model->get_by_id($laporan_harian_id);
        $dinas = $this->Dinas_model->getDinas($laporanharian['dinas_id']);
        $kru = $this->User_model->get_by_dinas_id($laporanharian['dinas_id']);

        foreach ($pemasukan_array as $p) {
            $pemasukan += intval($p['tarif']);
        }

        $daftar_pengeluaran = array();
        foreach ($pengeluaran_array as $p) {
            $pengeluaran += intval($p['nominal']);
            $row = array(
                "nama_pengeluaran" => $p['nama_pengeluaran'],
                "nominal" => number_format($p['nominal'], 0, ",", ".")
            );
            $daftar_pengeluaran[] = $row;
        }

        $sisa_pendapatan = $pemasukan - $pengeluaran;

        if ($dinas['kelas'] == "Ekonomi") {
            foreach ($kru as $k) {
                if ($k['role_id'] == 31) {
                    $nip_nama_sopir = "[" . $k['nip'] . "] " . $k['nama'];
                    $nama_sopir = $k['nama'];
                    $premi_sopir = $sisa_pendapatan * 4 / 100;
                } else if ($k['role_id'] == 32) {
                    $nip_nama_kondektur = "[" . $k['nip'] . "] " . $k['nama'];
                    $nama_kondektur = $k['nama'];
                    $premi_kondektur = $sisa_pendapatan * 3 / 100;
                } else {
                    $nip_nama_kernet = "[" . $k['nip'] . "] " . $k['nama'];
                    $nama_kernet = $k['nama'];
                    $premi_kernet = $sisa_pendapatan * 2 / 100;
                }
            }
        } else {
            foreach ($kru as $k) {
                if ($k['role_id'] == 31) {
                    $nip_nama_sopir = "[" . $k['nip'] . "] " . $k['nama'];
                    $nama_sopir = $k['nama'];
                    $premi_sopir = $sisa_pendapatan * 5 / 100;
                } else {
                    $nip_nama_kondektur = "[" . $k['nip'] . "] " . $k['nama'];
                    $nama_kondektur = $k['nama'];
                    $premi_kondektur = $sisa_pendapatan * 4 / 100;
                }
            }
        }

        $jumlah_premi = $premi_sopir + $premi_kondektur + $premi_kernet;
        $setoran_kas = $sisa_pendapatan - $jumlah_premi;
        $setoran_kas_terbilang = terbilang($setoran_kas);

        $data = array(
            "nopol" => $dinas['nopol'],
            "trayek" => $dinas['posawal'] . " - " . $dinas['posakhir'],
            "kelas" => $dinas['kelas'],

            "waktu_berangkat" => date_format(date_create($laporanharian['tanggal_jalan']), 'H:i') . " - " . longdate_indo($laporanharian['tanggal_jalan']),

            "nip_nama_sopir" => $nip_nama_sopir,
            "nip_nama_kondektur" => $nip_nama_kondektur,
            "nip_nama_kernet" => $nip_nama_kernet,

            "pendapatan_rit" => number_format($pemasukan, 0, ",", "."),
            "daftar_pengeluaran" => $daftar_pengeluaran,
            "pengeluaran" => number_format($pengeluaran, 0, ",", "."),
            "sisa_pendapatan" => number_format($sisa_pendapatan, 0, ",", "."),

            "nama_sopir" => $nama_sopir,
            "nama_kondektur" => $nama_kondektur,
            "nama_kernet" => $nama_kernet,

            "premi_sopir" => number_format($premi_sopir, 0, ",", "."),
            "premi_kondektur" => number_format($premi_kondektur, 0, ",", "."),
            "premi_kernet" => number_format($premi_kernet, 0, ",", "."),
            "jumlah_premi" => number_format($jumlah_premi, 0, ",", "."),

            "setoran_kas_angka" => number_format($setoran_kas, 0, ",", "."),
            "setoran _kas_terbilang" => $setoran_kas_terbilang . " rupiah",
            "status_selesai" => $laporanharian['status_selesai']
        );

        if ($data) {
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
    }
}
