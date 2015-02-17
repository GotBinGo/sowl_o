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
		if($res->avatar == NULL)
			$res->avatar = "upload/img/default_user.png";
		else
			$res->avatar = "upload/img/user/" . $res->avatar;

		$res->last_login = $row["last_login"];


		return $res;
	}
}

class UserHandle
{
	public $obj;
	public $id;
	private $manager;
	public function __construct($manager, $id, $obj = NULL)
	{
		$this->id = $id;
		$this->manager = $manager;
		$this->obj = $obj;
	}

	public function get()
	{
		if(isset($this->obj) && $this->obj != NULL)
			return $this->obj;
		else
		{
			return $this->obj = $this->manager->users->retrieveByID($this->id);
		}
	}

	public function getViewablePlaylists()
	{
		return $this->manager->playlists->byCondition("public OR user_id = '$this->id'");
	}

	public function getPlaylists()
	{
		return $this->manager->playlists->byUserID($this->id);
	}
}

class UserManager
{
	private $manager;

	public function __construct($manager)
	{
		$this->manager = $manager;
	}

	public function authenticate($username, $password)
	{
		$uname = $this->manager->escapeString($username);
		$passw = $this->manager->escapeString($password);

		$row = $this->manager->getRecord("users", "name = '$uname'");
		if($row === FALSE)
			return FALSE;

		$pwhash = md5($row["salt"] . $password);
		if($pwhash != $row["password"])
			return FALSE;

		return new UserHandle($this->manager, $row["id"],
			User::fromDBRecord($row));
	}

	public function login($userhnd)
	{
		$value = date("Y-m-d H:i:s");
		$field = new DBField("last_login", $value);
		return $this->manager->updateTable("users", "id = '$userhnd->id'", array( $field ));
	}

	public function retrieveByID($id)
	{
		$row = $this->manager->getRecord("users", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return User::fromDBRecord($row);
	}

	public function byID($id)
	{
		return new UserHandle($this->manager, $id);
	}

}

?>
