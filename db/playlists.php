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
		$this->id = $manager->escape($id);
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
		$result = $this->manager->getTable("playlists_tracks, tracks", "playlists_tracks.playlist_id = '$this->id' AND tracks.id = playlists_tracks.track_id");
		$result = $this->manager->prependTableNames($result);
		if($result === FALSE)
			return FALSE;

		$res = array();

		foreach($result as $record)
		{
			$res[] = new TrackHandle($this->manager, $record["tracks.id"],
				Track::fromDBRecord($record, "tracks"));
		}
		return $res;
	}

	public function setPublic($public)
	{
		$public = ($public == "true" || $public == "1") ? TRUE : FALSE;
		$field = new DBField("public", $public ? 1 : 0);
		return $this->manager->updateTable("playlists", "id = '$this->id'", array($field));
	}

	public function insert($trackid)
	{
		$fields = array(
			new DBField("playlist_id", $this->id),
			new DBField("track_id", $this->manager->escape($trackid)));
		return $this->manager->insertToTable("playlists_tracks", $fields);
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
		$user_id = $this->manager->escape($user_id);
		return $this->byCondition("user_id = '$user_id'");
	}

	public function byCondition($condition = "", $limit = 0)
	{
		$result = $this->manager->getTable("playlists", $condition, $limit);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_assoc())
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
		$q1 = ($user && $user->id) ? "user_id = '$user->id' OR public" : "public";
		if(!$query || $query == "")
		{
			return $this->byCondition($q1, $limit);
		}

		$query = $this->manager->escape($query);
		$terms = explode($query, " ");
		$conditions = array();
		foreach($terms as $current)
			$conditions[] = "name LIKE '%$current%'";
		$query = implode(" OR ", $conditions);
		$query = "(" . $query . ") AND ( $q1 )";

		return $this->byCondition($query, $limit);
	}

	public function create($user, $name)
	{
		$fields = array(
			new DBField("name", $this->manager->escape($name)),
			new DBField("user_id", $user->id)
		);

		return $this->manager->insertToTable("playlists", $fields);
	}
}

?>
