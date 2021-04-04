<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['nip']) and isset($_GET['password'])) {
        $db = new DbOperations();

        if ($db->userProfil($_GET['nip'], $_GET['password'])) {
            $user = $db->getUserByNip($_GET['nip']);
            $response['error'] = false;
            $response['nama'] = $user['nama'];
            $response['nip'] = $user['nip'];
            $response['password'] = $user['password'];
            http_response_code(200);
        } else {
            $response['error'] = true;
            $response['message'] = "NIP atau password salah";
            http_response_code(404);
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Silahkan isi kolom secara lengkap";
        http_response_code(400);
    }
}

echo json_encode($response);
