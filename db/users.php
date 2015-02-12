<?php
require_once("config.php");

class User
{
	public $id;
	public $name;
	public $display_name;
	public $fbid;
	public $avatar;
	public $last_login;

	public static function fromDBRecord($row)
	{
		$res = new User();
		$res->id = $row["id"];
		$res->name = $row["name"];
		$res->display_name = $row["display_name"];
		$res->fbid = $row["fbid"];
		$res->avatar = $row["avatar"];
		$res->last_login = $row["last_login"];

		return $res;
	}
}

class UserHandle
{
	public $obj;
	public $id;
	private $manager;
	public function __construct($manager, $id, $obj)
	{
		$this->id = $id;
		$this->manager = $manager;
		$this->obj = $obj;
	}

	public function get()
	{
		if(isset($this->obj))
			return $this->obj;
		else
		{
			return $this->manager->retrieveByID($this->id);
		}
	}
}

class UserManager
{
	// does not own connection
	private $conn;
	private $manager;

	public function __construct($conn, $manager)
	{
		$this->conn = $conn;
		$this->manager = $manager;
	}

	public function authenticate($username, $password)
	{
		$uname = $this->conn->escape_string($username);
		$passw = $this->conn->escape_string($password);

		$row = $this->manager->getRecord("users", "name = '$uname'");
		if($row === FALSE)
			return FALSE;

		$pwhash = md5($row["salt"] . $password);
		if($pwhash != $row["password"])
			return FALSE;

		return new UserHandle($this, $row["id"],
			User::fromDBRecord($row));
	}

	public function retrieveByID($id)
	{
		$row = $this->manager->getRecord("users", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return new UserHandle($this, $row["id"],
			User::fromDBRecord($row));
	}

}

?>
