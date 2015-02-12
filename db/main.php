<?php
require_once("config.php");
class DatabaseConnection
{
	private $conn;

	public function __construct()
	{
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$this->conn->query("set names 'utf8';");
		$this->conn->set_charset("utf8");
		$this->conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
		$this->conn->query("SET time_zone = '+00:00';");
	}

	function __destruct()
	{
		$this->conn->close();
	}
}

$db = new DatabaseConnection();

?>
