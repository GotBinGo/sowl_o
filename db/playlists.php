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

		return $res;
	}
}

class PlaylistManager
{
	private $conn;
	private $manager;

	public function __construct($conn, $manager)
	{
		$this->conn = $conn;
		$this->manager = $manager;
	}

	public function byID($id)
	{
		$row = $this->manager->getRecord("playlists", "id = '$id'");
		if($row === FALSE)
			return FALSE;

		return User::fromDBRecord($row);
	}
}

?>
