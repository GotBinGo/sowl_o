<?php
// MySQL installer
//  usage:
//    edit line 12 to contain your root password
//    run
//    delete your MySQL root password from this file
//  alternative:
//    enable ncurses module and enter your root password at runtime

require_once('config.php');

define('MYSQL_ROOT_PASSWORD', '123456');

class Field
{
	public $nullable;
	public $type;
	public $name;
	public $auto_increment;
	public $primary_key;
	public function __construct($type, $name, $nullable, $auto_increment, $primary_key)
	{
		$this->type = $type;
		$this->name = $name;
		$this->nullable = $nullable;
		$this->auto_increment = $auto_increment;
		$this->primary_key = $primary_key;
	}

	public function getCreateLine()
	{
		return  $this->name ." "
			. $this->type 
			. ($this->type == "text" ? " character set utf8 collate utf8_general_ci " : " ")
			. ($this->nullable ? "" : " not null") 
			. ($this->auto_increment ? " auto_increment" : "" );
	}
}

class Value
{
	public $name;
	public $value;
	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}
}

class Record
{
	public $values;
	public function __construct($values)
	{
		$this->values = $values;
	}

	public function insertToTable($conn, $table)
	{
		$querystr = "insert into '$table' ";
		$namesstr = "(";
		$valuesstr = "(";
		foreach($this->values as $current)
		{
			$namesstr .= $current->name . ", ";
			$valuesstr .= "'$current->value', ";
		}
		$namesstr[strlen($namesstr) - 1] = ')';
		$valuesstr[strlen($valuesstr) - 1] = ')';
		$querystr .= $namesstr . " VALUES" . $valuesstr . ";";

		$conn->query($querystr);
	}
}

function create_table($conn, $name, $fields)
{
	$conn->query("drop table if exists " . $name . ";");
	
	$querystr = "create table if not exists " . $name . " (";
	$keys = array();
	foreach($fields as $field)
	{
		$querystr .= $field->getCreateLine() . ", ";
		if($field->primary_key)
			$keys[] = $field;
	}
	if(sizeof($keys) > 0)
	{
		$keysstr = "primary key(";
		foreach($keys as $key)
			$keysstr .= $key->name . ",";
		$keysstr = substr($keysstr, 0, strlen($keysstr) - 1);
		$keysstr .= ") ";
		$querystr .= $keysstr;
	}
	else
		echo("error in create_table: no primary key");

	$querystr .= " ) default charset=utf8;";

	$result = $conn->query($querystr);
	if($result === false)
		echo("couldn't create table " . $name . ": " . $conn->error);

	$conn->query("delete from '$name';");
	$conn->query("alter table '$name' auto_increment=1;");
}

function create_user($conn, $username, $host = "localhost", $password = "123456")
{
	$result = $conn->query("create user '$username'@'$host' identified by '$password';");
	$result2 = false;
	if($result !== false)
		$result2 = $conn->query("grant all privileges on *.* to '$username'@'$host' with grant option;");

	return $result && $result2;
}

function get_single_result($result, $index = 0, $field = 0)
{
	$result->data_seek($index);
	$row = $result->fetch_array();
	return $row[$field];
}

function create_database($conn, $db_name)
{
	$querystr = "create database if not exists ".$db_name.";";
	$result = $conn->query($querystr);
	if($result === false)
		echo("error while trying to create database: " . $conn->error . "\n");
}

function use_database($conn, $db_name)
{
	$result = $conn->query("use '$db_name';");
	if($result === false)
		echo("error while trying to select database: " . $conn->error . "\n");
}

if(MYSQL_ROOT_PASSWORD == '' && extension_loaded("ncurses"))
{
	echo("Enter MySQL root password: ");
	if(extension_loaded("ncurses"))
		ncurses_noecho();

	$root_pass = fgets(STDIN);

	if(extension_loaded("ncurses"))
		ncurses_echo();
}
else
	$root_pass = MYSQL_ROOT_PASSWORD;

$user_found = false;
if($root_pass != '')
{
	$conn = new mysqli(DB_HOST, 'root', $root_pass);
	$result = $conn->query("select User from mysql.user where User = '" . DB_USER . "';");
	if($result === false)
		echo("couldn't check user list: " . $conn->error . "\n");

	if(get_single_result($result) == DB_USER)
		$user_found = true;

	if(!$user_found)
	{
		$result = create_user($conn, DB_USER, DB_HOST, DB_PASS);

		if($result == false)
			echo("couldn't create MySQL user");
	}

	create_database($conn, DB_NAME);

	$conn->close();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8"); //mysqli milyen kodolast var
$conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
$conn->query("SET time_zone = '+00:00';");





create_table($conn, "playlists", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",         false,   true,          true),
		new Field( "varchar(255)",   "name",       false,   false,         false),
		new Field( "int(11)",        "user_id",    false,   false,         false),
		new Field( "tinyint(1)",     "public",     false,   false,         false),
		new Field( "varchar(255)",   "avatar",     true,    false,         false)
	));

create_table($conn, "playlists_tracks", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",         false,   true,          true),
		new Field( "int(11)",        "playlist_id",false,   false,         false),
		new Field( "int(11)",        "track_id",   false,   false,         false)
	));

create_table($conn, "tracks", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",         false,   true,          true),
		new Field( "text",           "file_name",  false,   false,         false),
		new Field( "text",           "author_name",false,   false,         false),
		new Field( "text",           "track_name", false,   false,         false),
		new Field( "int(11)",        "track_length",false,  false,         false),
		new Field( "int(11)",        "user_id",    false,   false,         false),
		new Field( "date",           "upload_date",false,   false,         false),
		new Field( "text",           "file_type",  false,   false,         false)
	));

create_table($conn, "tags", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "track_id",   false,   false,          true),
		new Field( "varchar(255)",   "tag",        false,   false,          true)
	));

create_table($conn, "ratings", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "user_id",   false,   false,          true),
		new Field( "int(11)",        "track_id",  false,   false,          true),
		new Field( "int(11)",        "rating",    false,   false,          false)
	));

create_table($conn, "track_comments", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",        false,   true,           true),
		new Field( "int(11)",        "user_id",   false,   false,          false),
		new Field( "int(11)",        "track_id",  false,   false,          false),
		new Field( "text",           "comment",   false,   false,          false)
	));

create_table($conn, "playlist_comments", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",        false,   true,           true),
		new Field( "int(11)",        "user_id",   false,   false,          false),
		new Field( "int(11)",        "playlist_id",false,  false,          false),
		new Field( "text",           "comment",   false,   false,          false)
	));

create_table($conn, "users", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "id",         false,   true,          true),
		new Field( "varchar(255)",   "name",       false,   false,         false),
		new Field( "varchar(255)",   "password",   false,   false,         false),
		new Field( "varchar(255)",   "salt",       false,   false,         false),
		new Field( "varchar(255)",   "fbid",        true,   false,         false),
		new Field( "varchar(255)",   "display_name",false,  false,         false),
		new Field( "varchar(255)",   "avatar",      true,   false,         false),
		new Field( "datetime",       "last_login",  true,   false,         false),
	));

create_table($conn, "followings", 
	array(
		//         type              name          nullable auto_increment primary_key
		new Field( "int(11)",        "follower_id",false,   false,          true),
		new Field( "int(11)",        "followed_id",false,   false,          true)
	));

$conn->query("insert into users (name, password, display_name) VALUES".
	"('root', 'e10adc3949ba59abbe56e057f20f883e', 'Alapértelmezett felhasználó');");

(new Record(array(
	new Value("name", "root"),
	new Value("password", "e10adc3949ba59abbe56e057f20f883e"),
	new Value("display_name", "Alapértelmezett felhasználó")
)))->insertToTable($conn, "users");


$fn_query_string = "DROP FUNCTION IF EXISTS `levenshtein`;
CREATE FUNCTION `levenshtein`(`s1` VARCHAR(255) CHARACTER SET utf8, `s2` VARCHAR(255) CHARACTER SET utf8)
	RETURNS TINYINT UNSIGNED
	NO SQL
	DETERMINISTIC
BEGIN
	DECLARE s1_len, s2_len, i, j, c, c_temp TINYINT UNSIGNED;
	DECLARE cv0, cv1 VARBINARY(256);
	
	IF (s1 + s2) IS NULL THEN
		RETURN NULL;
	END IF;
	
	SET s1_len = CHAR_LENGTH(s1),
		s2_len = CHAR_LENGTH(s2),
		cv1 = 0x00,
		j = 1,
		i = 1,
		c = 0;
	
	IF (s1 = s2) THEN
		RETURN 0;
	ELSEIF (s1_len = 0) THEN
		RETURN s2_len;
	ELSEIF (s2_len = 0) THEN
		RETURN s1_len;
	END IF;
	
	WHILE (j <= s2_len) DO
		SET cv1 = CONCAT(cv1, CHAR(j)),
		j = j + 1;
	END WHILE;
	
	WHILE (i <= s1_len) DO
		SET c = i,
			cv0 = CHAR(i),
			j = 1;
		
		WHILE (j <= s2_len) DO
			SET c = c + 1;
			
			SET c_temp = ORD(SUBSTRING(cv1, j, 1)) -- ord of cv1 current char
				+ (NOT (SUBSTRING(s1, i, 1) = SUBSTRING(s2, j, 1))); -- different chars? (NULL-safe)
			IF (c > c_temp) THEN
				SET c = c_temp;
			END IF;
			
			SET c_temp = ORD(SUBSTRING(cv1, j+1, 1)) + 1;
			IF (c > c_temp) THEN
				SET c = c_temp;
			END IF;
			
			SET cv0 = CONCAT(cv0, CHAR(c)),
				j = j + 1;
		END WHILE;
		
		SET cv1 = cv0,
			i = i + 1;
	END WHILE;
	
	RETURN c;
END;


DROP FUNCTION IF EXISTS `levenshtein_ratio`;
CREATE FUNCTION `levenshtein_ratio`(`s1` VARCHAR(255) CHARACTER SET utf8, `s2` VARCHAR(255) CHARACTER SET utf8)
	RETURNS TINYINT UNSIGNED
	DETERMINISTIC
	NO SQL
	COMMENT 'Levenshtein ratio between strings'
BEGIN
	DECLARE s1_len TINYINT UNSIGNED DEFAULT CHAR_LENGTH(s1);
	DECLARE s2_len TINYINT UNSIGNED DEFAULT CHAR_LENGTH(s2);
	RETURN ((levenshtein(s1, s2) / IF(s1_len > s2_len, s1_len, s2_len)) * 100);
END;


DROP FUNCTION IF EXISTS levenshtein_lentz;
CREATE FUNCTION levenshtein_lentz(s1 VARCHAR(255) CHARACTER SET utf8, s2 VARCHAR(255) CHARACTER SET utf8)
	RETURNS INT
	DETERMINISTIC
BEGIN
	DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
	DECLARE s1_char CHAR CHARACTER SET utf8;
	-- max strlen=255 for this function
	DECLARE cv0, cv1 VARBINARY(256);

	SET s1_len = CHAR_LENGTH(s1),
		s2_len = CHAR_LENGTH(s2),
		cv1 = 0x00,
		j = 1,
		i = 1,
		c = 0;

	IF (s1 = s2) THEN
		RETURN (0);
	ELSEIF (s1_len = 0) THEN
		RETURN (s2_len);
	ELSEIF (s2_len = 0) THEN
		RETURN (s1_len);
	END IF;

	WHILE (j <= s2_len) DO
		SET cv1 = CONCAT(cv1, CHAR(j)),
		j = j + 1;
	END WHILE;

	WHILE (i <= s1_len) DO
		SET s1_char = SUBSTRING(s1, i, 1),
		c = i,
		cv0 = CHAR(i),
		j = 1;

		WHILE (j <= s2_len) DO
			SET c = c + 1,
			cost = IF(s1_char = SUBSTRING(s2, j, 1), 0, 1);

			SET c_temp = ORD(SUBSTRING(cv1, j, 1)) + cost;
			IF (c > c_temp) THEN
				SET c = c_temp;
			END IF;

			SET c_temp = ORD(SUBSTRING(cv1, j+1, 1)) + 1;
			IF (c > c_temp) THEN
				SET c = c_temp;
			END IF;

			SET cv0 = CONCAT(cv0, CHAR(c)),
				j = j + 1;
		END WHILE;

		SET cv1 = cv0,
		i = i + 1;
	END WHILE;

	RETURN (c);
END;
";

$result = $conn->multi_query($fn_query_string);

if($result === false)
	echo("failed to add function levenshtein_ratio: " . $conn->error . "\n");


$conn->close();

echo("done.\n");

?>
