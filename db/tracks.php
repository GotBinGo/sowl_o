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
		$res->id = $row[$tablename . "id"];
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
		$this->id = $manager->escape($id);
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

		while($record = $result->fetch_assoc())
		{
			$res[] = new TrackHandle($this->manager, $record["id"],
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
		$query = $this->manager->escape($query);
		$usercheck = ($user && $user->id) ? "tracks.user_id = '$user->id' OR playlists.public" : "playlists.public";
		$usercheck = "";
		$joincondition = "tracks.id = playlists_tracks.track_id AND playlists.id = playlists_tracks.playlist_id";

		if($query && $query != "")
		{
			$terms = explode($query, " ");
			$query = $this->manager->escape($query);
			$conditions = array();
			foreach($terms as $current)
				$conditions[] = "tracks.author_name LIKE '%$current%' OR tracks.track_name LIKE '%$current%'";
			$query = implode(" OR ", $conditions);
			$query = "(" . $query . ") AND ( $usercheck }";
		}
		else
		{
			$query = $usercheck;
		}

		$result = $this->manager->getTable("tracks LEFT JOIN (playlists_tracks, playlists) ON ($joincondition)", $query, $limit);
		if($result === FALSE)
			return FALSE;

		$result = $this->manager->prependTableNames($result);

		$res = array_map(function($item) {
			return new TrackHandle($this->manager, $item["tracks.id"], Track::fromDBRecord($item, "tracks"));
		}, $result);

		return $res;
	}

	public function add($user, $tmpname, $extension, $author, $title, $length, $filetype, $tags)
	{
		$upload_date = date("Y-m-d H:i:s");
		$newname = md5(microtime().rand()) . $extension;
		$tags = array_filter(array_unique($tags));
		$author = $this->manager->escape($author);
		$title = $this->manager->escape($title);
		$length = $this->manager->escape($length);

		$track_fields = array(
			new DBField("file_name", $newname),
			new DBField("author_name", $author),
			new DBField("track_name", $title),
			new DBField("track_length", $length),
			new DBField("user_id", $user->id),
			new DBField("upload_date", $upload_date),
			new DBField("file_type", $filetype)
		);

		$result = $this->manager->insertToTable("tracks", $track_fields);
		if($result === FALSE)
			return FALSE;

		$track_id = $result;

		$result = move_uploaded_file($tmpname, "../upload/uploads/$newname");
		if(!$result)
		{
			$this->manager->deleteFromTable("tracks", "id = '$track_id'");
			$this->manager->error = "Couldn't move uploaded file";
			return FALSE;
		}

		$error = 0;
		foreach($tags as $tag)
		{
			$tag_fields = array(
				new DBField("track_id", $track_id),
				new DBField("tag", $this->manager->escape($tag))
			);
			$result = $this->manager->insertToTable("tags", $tag_fields);
			if($result === FALSE)
				$error++;
		}
		if($error > 0)
		{
			$this->manager->error = "Couldn't add $error tags to the uploaded track";
			return FALSE;
		}

		return $track_id;
	}
}

?>
