<?php
require_once("config.php");
class DatabaseConnection
{
	private $conn;

	public function __construct()
	{
		try {
			$this->conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS,
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "set names 'utf8'"
				)
			);
		}
		catch(PDOException $e)
		{
			echo("DB connection error: " . $e->getMessage() . "<br />");
			die();
		}
	}
}

$db = new DatabaseConnection();

?>
