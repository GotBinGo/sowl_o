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

	public function getPlaylistsVisibleTo($user)
	{
		if(!$user)
			return $this->getPublicPlaylists();

		if($user->id == $this->id)
			return $this->getPlaylists();

		return $user->getPublicPlaylists();
	}

	public function getPublicPlaylists()
	{
		return $this->manager->playlists->byCondition("public AND user_id = '$this->id'");
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

		while($record = $result->fetch_array())
		{
			$res[] = new UserHandle($this, $record["id"],
				User::fromDBRecord($record));
		}

		return $res;
	}

	public function retrieveByID($id)
	{
		if(!isset($id) || $id == NULL)
			return FALSE;

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
		$name = $this->manager->escapeString($name);
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

		$terms = explode($query, " ");
		$conditions = array();
		foreach($terms as $current)
			$conditions[] = "display_name LIKE '%$current%'";
		$condition = implode(" OR ", $conditions);

		return $this->byCondition($condition, $limit);
	}
}

?>
