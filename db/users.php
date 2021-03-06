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

	public static function fromDBRecord($row, $tablename = "")
	{
		if($tablename && $tablename != "")
			$tablename = $tablename . ".";

		$res = new User();
		$res->id = $row[$tablename . "id"];
		$res->name = $row[$tablename . "name"];
		$res->display_name = $row[$tablename . "display_name"];
		$res->fbid = $row[$tablename . "fbid"];

		$res->avatar = $row[$tablename . "avatar"];
		if($res->avatar == NULL)
			$res->avatar = "upload/img/default_user.png";
		else
			$res->avatar = "upload/img/user/" . $res->avatar;

		$res->last_login = $row[$tablename . "last_login"];


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
		$this->manager = $manager;
		$this->id = $this->manager->escape($id);
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

	public function viewablePlaylists()
	{
		return $this->manager->playlists->byCondition("public OR user_id = '$this->id'");
	}

	public function playlistsVisibleTo($user)
	{
		if(!$user)
			return $this->publicPlaylists();

		if($user->id == $this->id)
			return $this->playlists();

		return $this->publicPlaylists();
	}

	public function publicPlaylists()
	{
		return $this->manager->playlists->byCondition("public = '1' AND user_id = '$this->id'");
	}

	public function playlists()
	{
		return $this->manager->playlists->byUserID($this->id);
	}

	public function tracks()
	{
		return $this->manager->tracks->byUserID($this->id);
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
		$uname = $this->manager->escape($username);

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

	public function byCondition($condition, $limit = 0)
	{
		$result = $this->manager->getTable("users", $condition, $limit);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_assoc())
		{
			$res[] = new UserHandle($this->manager, $record["id"],
				User::fromDBRecord($record));
		}

		return $res;
	}

	public function retrieveByID($id)
	{
		if(!isset($id) || $id == NULL)
			return FALSE;

		$id = $this->manager->escape($id);
		$row = $this->manager->getRecord("users", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return User::fromDBRecord($row);
	}

	public function byID($id)
	{
		if(!isset($id) || $id == NULL)
			return FALSE;

		return new UserHandle($this->manager, $id);
	}

	public function byName($name)
	{
		$name = $this->manager->escape($name);
		$row = $this->manager->getRecord("users", "name = '$name'");
		return new UserHandle($this->manager, $row['id'],
			User::fromDBRecord($row));
	}

	public function search($query, $limit = 0)
	{
		if(!$query || $query == "")
		{
			return $this->byCondition("", $limit);
		}

		$query = $this->manager->escape($query);
		$terms = explode($query, " ");
		$conditions = array();
		foreach($terms as $current)
			$conditions[] = "display_name LIKE '%$current%'";
		$condition = implode(" OR ", $conditions);

		return $this->byCondition($condition, $limit);
	}

	public function add($display_name, $username, $password)
	{
		unset($this->manager->error);
		if(!$display_name || !$username || !$password)
		{
			$this->manager->error = "invalid parameters";
			return FALSE;
		}
		$display_name = $this->manager->escape($display_name);
		$username = $this->manager->escape($username);
		$salt = md5(microtime().rand());
		$pwhash = md5($salt . $password);
		$fields = array(
			new DBField("name", $username),
			new DBField("password", $pwhash),
			new DBField("salt", $salt),
			new DBField("display_name", $display_name)
		);

		$result = $this->manager->insertToTable("users", $fields);
		return $result;
	}
}

?>
