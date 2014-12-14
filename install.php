<?php
// MySQL installer
//  usage:
//    edit line 12 to contain your root password
//    run
//    delete your MySQL root password from this file
//  alternative:
//    enable ncurses module and enter your root password at runtime

require_once('config.php');

define('MYSQL_ROOT_PASSWORD', '');


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

if(MYSQL_ROOT_PASSWORD == '' && extension_loaded("ncurses"))
{
	echo("Enter MySQL root password: ");
	if(extension_loaded("ncurses"))
		ncurses_noecho();

	$root_pass = fgets(STDIN);

	if(extension_loaded("ncurses"))
		ncurses_echo();
}
elseif(MYSQL_ROOT_PASSWORD == '')
{
	echo("No MySQL root password found in install.php, skipping user creation.\n");
	$root_pass = '';
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

	$conn->close();
}

require_once("conn.php");
$conn->set_charset("utf8"); //mysqli milyen kodolast var
$conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
$conn->query("SET time_zone = '+00:00';");

$result = $conn->query("create database if not exists " . DB_NAME . ";");
if($result === false)
	echo("error while trying to create database: " . $conn->error . "\n");

$result = $conn->query("use " . DB_NAME . ";");
if($result === false)
	echo("error while trying to select database: " . $conn->error . "\n");

$result = $conn->query("create table if not exists playlists( ".
	"id int(11) not null auto_increment, ".
	"name varchar(255) not null, ".
	"user_id int(11) not null, ".
	"public tinyint(1) not null, ".
	"avatar varchar(255) not null, ".
	"primary key(id) ".
	") default charset=utf8;");
if($result === false)
	echo("couldn't create table 'playlists': " . $conn->error . "\n");

$result = $conn->query("create table if not exists playlist_track ( ".
	"id int(11) not null auto_increment, ".
	"playlist_id int(11) not null, ".
	"track_id int(11) not null, ".
	"primary key(id) ".
	") default charset=utf8;");
if($result === false)
	echo("couldn't create table 'playlist_track': " . $conn->error . "\n");

$result = $conn->query("create table if not exists tracks ( ".
	"id int(11) not null auto_increment, ".
	"file_name text character set utf8 collate utf8_general_ci not null, ".
	"author_name text character set utf8 collate utf8_general_ci not null, ".
	"track_name text character set utf8 collate utf8_general_ci not null, ".
	"user_id int(11) not null, ".
	"upload_date date not null, ".
	"file_type text not null, ".
	"primary key(id) ".
	") default charset=utf8;");
if($result === false)
	echo("couldn't create table 'tracks': " . $conn->error . "\n");

$result = $conn->query("create table if not exists users ( ".
	"id int(11) not null auto_increment, ".
	"name varchar(255) not null, ".
	"password varchar(255) not null, ".
	"salt varchar(255) not null, ".
	"fbid varchar(255) not null, ".
	"display_name varchar(255) not null, ".
	"avatar varchar(255) not null, ".
	"primary key(id) ".
	") default charset=utf8;");
if($result === false)
	echo("couldn't create table 'users': " . $conn->error . "\n");


$conn->query("alter table 'users' auto_increment = 1;");
$conn->query("alter table 'tracks' add key track_id(id);");
$conn->query("alter table 'playlists' auto_increment = 1;");

$conn->query("delete from users;");
$conn->query("insert into users (name, password, display_name) VALUES".
	"('root', 'e10adc3949ba59abbe56e057f20f883e', 'Alapértelmezett felhasználó');");

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
