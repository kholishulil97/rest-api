<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['nip']) and isset($_POST['password'])) {
		$db = new DbOperations();

		if ($db->userLogin($_POST['nip'], $_POST['password'])) {
			$user = $db->getUserByNip($_POST['nip']);
			$response['error'] = false;
			$response['nama'] = $user['nama'];
			$response['nip'] = $user['nip'];
			$response['url'] = $user['fotoprofil'];
		} else {
			$response['error'] = true;
			$response['message'] = "NIP atau password salah";
		}
	} else {
		$response['error'] = true;
		$response['message'] = "Silahkan isi kolom secara lengkap";
	}
}

echo json_encode($response);
