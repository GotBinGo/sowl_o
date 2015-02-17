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

	public static function fromDBRecord($row)
	{
		$res = new Track();
		$res->id = $row["id"];
		$res->file_name = $row["file_name"];
		$res->author = $row["author_name"];
		$res->title = $row["track_name"];
		$res->length = $row["track_length"];
		$res->uploader_id = $row["user_id"];
		$res->upload_date = $row["upload_date"];
		$res->mimetype = $row["file_type"];

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
		$this->manager == $manager;
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
}

?>
