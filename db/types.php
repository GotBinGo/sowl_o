<?php
require_once("config.php");
require_once("db/users.php");
class DatabaseConnection
{
	//private $conn;
	public $conn;
	public $users;
	public $error;

	public function __construct()
	{
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$this->conn->query("set names 'utf8';");
		$this->conn->set_charset("utf8");
		$this->conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
		$this->conn->query("SET time_zone = '+00:00';");

		$this->users = new UserManager($this->conn, $this);
	}

	function __destruct()
	{
		@$this->conn->close();
	}

	// Utility functions for classes
	//
	// retrieve table data, optionally conditionally
	public function getTable($name, $condition)
	{
		unset($this->error);
		if(isset($condition) && $condition != "")
			$condition = " WHERE " . $condition;

		$result = $this->conn->query("SELECT * FROM $name" . $condition . ";");
		if($result === FALSE)
			$this->error = "Query error: " . $this->conn->error;

		return $result;
	}

	// Same as above but for a single record only
	public function getRecord($name, $condition)
	{
		$result = $this->getTable($name, $condition);
		if($result === FALSE)
			return FALSE;
		if($result->num_rows == 0)
		{
			$this->error = "Record not found";
			return FALSE;
		}
		if($result->num_rows != 1)
		{
			$this->error = "Too many records";
			return FALSE;
		}
		return $result->fetch_array();
	}
}

?>
