<?php
require_once("config.php");

class Track
{
	public $id;
	public $file_name;
	public $author;
	public $title;
	public $length;
	public $uploader_id;
	public $upload_date;
	public $mimetype;

	public static function fromDBRecord($row, $tablename = "")
	{
		if($tablename && $tablename != "")
			$tablename = $tablename . ".";
		$res = new Track();
		$res->id = $row["id"];
		$res->file_name = $row[$tablename . "file_name"];
		$res->author = $row[$tablename . "author_name"];
		$res->title = $row[$tablename . "track_name"];
		$res->length = $row[$tablename . "track_length"];
		$res->uploader_id = $row[$tablename . "user_id"];
		$res->upload_date = $row[$tablename . "upload_date"];
		$res->mimetype = $row[$tablename . "file_type"];

		return $res;
	}
}

class TrackHandle
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
			return $this->obj = $this->manager->tracks->retrieveByID($this->id);
	}

	public function getUploader()
	{
		$obj = $this->get();
		return $this->manager->users->byID($obj->uploader_id);
	}
}

class TrackManager
{
	private $manager;

	public function __construct($manager)
	{
		$this->manager = $manager;
	}

	public function retrieveByID($id)
	{
		$row = $this->manager->getRecord("tracks", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return Track::fromDBRecord($row);
	}

	public function byID($id)
	{
		return new TrackHandle($this->manager, $id);
	}

	public function byCondition($condition, $limit = 0)
	{
		$result = $this->manager->getTable("tracks", $condition, $limit);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_array())
		{
			$res[] = new TrackHandle($this, $record["id"],
				Track::fromDBRecord($record));
		}

		return $res;
	}

	public function byUserID($userid, $limit = 0)
	{
		return $this->byCondition("user_id = '$userid'", $limit);
	}

	public function search($user, $query, $limit = 0)
	{
		$terms = explode($query, " ");
		$conditions = array();
		foreach($terms as $current)
			$conditions[] = "tracks.author_name LIKE '%$current%' OR tracks.track_name LIKE '%$current%'";
		$query = implode(" OR ", $conditions);
		$usercheck = ($user && $user->id) ? "playlists.user_id = '$user->id' OR playlists.public" : "playlists.public";
		$query = "(" . $query . ") AND ( tracks.id = playlists_tracks.track_id AND playlists.id = playlists_tracks.playlist_id "
			. "AND ($usercheck) )";

		$result = $this->manager->getTable("tracks, playlists, playlists_tracks", $condition, $limit);
		if($result === FALSE)
			return FALSE;

		$res = array();

		while($record = $result->fetch_array())
		{
			$res[] = new TrackHandle($this, $record["id"],
				Track::fromDBRecord($record, "tracks"));
		}

		return $res;
	}
}

?>
