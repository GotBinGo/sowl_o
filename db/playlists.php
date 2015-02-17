<?php
require_once("config.php");

class Playlist
{
	public $id;
	public $name;
	public $owner_id;
	public $isPublic;
	public $avatar;

	public static function fromDBRecord($row)
	{
		$res = new Playlist();
		$res->id = $row["id"];
		$res->name = $row["name"];
		$res->owner_id = $row["user_id"];
		$res->isPublic = $row["public"] ? TRUE : FALSE;

		$res->avatar = $row["avatar"];
		if($res->avatar == NULL)
			$res->avatar = "upload/img/default_user.png";
		else
			$res->avatar = "upload/img/user/" . $res->avatar;


		return $res;
	}
}

class PlaylistHandle
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
			return $this->obj = $this->manager->playlists->retrieveByID($this->id);
		}
	}
}

class PlaylistManager
{
	private $manager;

	public function __construct($manager)
	{
		$this->manager = $manager;
	}

	public function retrieveByID($id)
	{
		$row = $this->manager->getRecord("playlists", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return Playlist::fromDBRecord($row);
	}

	public function byID($id)
	{
		return new PlaylistHandle($this, $id);
	}

	public function byUserID($user_id)
	{
		return $this->byCondition("user_id = '$user_id'");
	}

	public function byCondition($condition)
	{
		$result = $this->manager->getTable("playlists", $condition);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_array())
		{
			$res[] = new PlaylistHandle($this, $record["id"],
				Playlist::fromDBRecord($record));
		}

		return $res;
	}

	public function getPublic()
	{
		return $this->byCondition("public");
	}
}

?>
