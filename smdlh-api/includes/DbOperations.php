<?php

class DbOperations
{

	private $con;

	function __construct()
	{

		require_once dirname(__FILE__) . '/DbConnect.php';

		$db = new DbConnect();

		$this->con = $db->connect();
	}

	/*CRUD -> C -> CREATE */

	public function createUser($username, $pass, $email)
	{
		if ($this->isUserExist($username, $email)) {
			return 0;
		} else {
			$password = md5($pass);
			$stmt = $this->con->prepare("INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES (NULL, ?, ?, ?);");
			$stmt->bind_param("sss", $username, $password, $email);

			if ($stmt->execute()) {
				return 1;
			} else {
				return 2;
			}
		}
	}

	public function userLogin($nip, $pass)
	{
		$stmt = $this->con->prepare("SELECT id FROM t_user WHERE nip = ? AND password = ?");
		$stmt->bind_param("ss", $nip, $pass);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}

	public function userProfil($nip, $pass)
	{
		$stmt = $this->con->prepare("SELECT id FROM t_user WHERE nip = ? AND password = ?");
		$stmt->bind_param("ss", $nip, $pass);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}

	public function getUserByNip($nip)
	{
		$stmt = $this->con->prepare("SELECT * FROM t_user WHERE nip = ?");
		$stmt->bind_param("s", $nip);
		$stmt->execute();
		return $stmt->get_result()->fetch_assoc();
	}


	private function isUserExist($nip, $email)
	{
		$stmt = $this->con->prepare("SELECT id FROM t_user WHERE nip = ? OR email = ?");
		$stmt->bind_param("ss", $nip, $email);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}
}
