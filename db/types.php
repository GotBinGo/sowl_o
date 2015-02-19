<?php
require_once("config.php");
require_once("db/users.php");
require_once("db/tracks.php");
require_once("db/playlists.php");

class DatabaseConnection
{
	//private $conn;
	public $conn;
	public $users;
	public $playlists;
	public $tracks;
	public $error;

	public function __construct()
	{
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if(!$this->conn)
			die('db connect error');
		if($this->conn->connect_error)
			die('connect error: ' . $this->conn->connect_error);
		$this->conn->query("set names 'utf8';");
		$this->conn->set_charset("utf8");
		$this->conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
		$this->conn->query("SET time_zone = '+00:00';");

		$this->users = new UserManager($this);
		$this->playlists = new PlaylistManager($this);
		$this->tracks = new TrackManager($this);
	}

	function __destruct()
	{
		@$this->conn->close();
		unset($this->conn);
	}

	// Utility functions for classes
	//
	// retrieve table data, optionally conditionally
	public function getTable($name, $condition, $limit = 0)
	{
		unset($this->error);
		if(isset($condition) && $condition != "")
			$condition = " WHERE " . $condition;

		if(isset($limit) && $limit != 0)
			$limit = " LIMIT " . $limit;
		else
			$limit = "";

		$sql = "SELECT * FROM $name" . $condition . $limit . ";";
		$result = $this->conn->query($sql);
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

	public function updateTable($name, $condition, $fields)
	{
		unset($this->error);
		if(isset($condition) && $condition != "")
			$condition = " WHERE " . $condition;

		$sql = "UPDATE $name " . DBField::aggregate($fields) . $condition . ";";
		$result = $this->conn->query($sql);
		if($result === FALSE)
			$this->error = "Failed to update table $name: " . $this->conn->error;

		return $result;
	}

	public function insertToTable($name, $fields)
	{
		unset($this->error);

		$sql = "INSERT INTO $name " . DBField::aggregate2($fields) . ";";
		$result = $this->conn->query($sql);
		if($result === FALSE)
			$this->error = "Failed to insert to table $name: " . $this->conn->error;

		return $result;
	}


	public function escape($var)
	{
		return gettype($var) == "string" ? $this->conn->escape_string($var) : $var;
	}
}

class DBField
{
	public $name;
	public $value;

	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}

	public function sql()
	{
		return $this->name . "='" . $this->value . "'";
	}

	public static function aggregate($array)
	{
		if(sizeof($array) == 0)
			return "";
		$res = "SET ";

		foreach($array as $field)
		{
			$res .= $field->sql() . ", ";
			$res = substr($res, 0, strlen($res) - 2);
			return $res;
		}
	}

	public static function aggregate2($array)
	{
		if(sizeof($array) == 0)
			return "";

		$names = "(";
		$values = "(";

		foreach($array as $field)
		{
			$names .= "$field->name,";
			$values .= "'$field->value',";
		}
		$names[strlen($names) - 1] = ")";
		$values[strlen($values) - 1] = ")";

		return $names . " VALUES " . $values;
	}
}

?>
