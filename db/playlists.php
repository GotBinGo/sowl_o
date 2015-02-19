<?php
require_once("config.php");

class Playlist
{
	public $id;
	public $name;
	public $owner_id;
	public $isPublic;
	public $avatar;

	public static function fromDBRecord($row, $tablename = "")
	{
		if($tablename && $tablename != "")
			$tablename = $tablename . ".";

		$res = new Playlist();
		$res->id = $row[$tablename . "id"];
		$res->name = $row[$tablename . "name"];
		$res->owner_id = $row[$tablename . "user_id"];
		$res->isPublic = $row[$tablename . "public"] ? TRUE : FALSE;

		$res->avatar = $row[$tablename . "avatar"];
		if($res->avatar == NULL)
			$res->avatar = "upload/img/default_list.png";
		else
			$res->avatar = "upload/img/list/" . $res->avatar;


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

	public function tracks()
	{
		$result = $this->manager->getTable("playlists_tracks, tracks", "playlists_tracks.playlist_id = '$this->id'");
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_array())
		{
			$res[] = new TrackHandle($this->manager, $record["id"],
				Track::fromDBRecord($record, "tracks"));
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
		return new PlaylistHandle($this->manager, $id);
	}

	public function byUserID($user_id)
	{
		return $this->byCondition("user_id = '$user_id'");
	}

	public function byCondition($condition = "", $limit = 0)
	{
		$result = $this->manager->getTable("playlists", $condition, $limit);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_array())
		{
			$res[] = new PlaylistHandle($this->manager, $record["id"],
				Playlist::fromDBRecord($record));
		}

		return $res;
	}

	public function getPublic()
	{
		return $this->byCondition("public");
	}

	public function search($user, $query, $limit = 0)
	{
		$q1 = ($user && $user->id) ? "user_id = '$user->id OR public" : "public";
		if(!$query || $query == "")
		{
			return $this->byCondition($q1, $limit);
		}

		$terms = explode($query, " ");
		$conditions = array();
		foreach($terms as $current)
			$conditions[] = "name LIKE '%$current%'";
		$query = implode(" OR ", $conditions);
		$query = "(" . $query . ") AND ( $q1 )";

		return $this->byCondition($query, $limit);
	}
}

?>
