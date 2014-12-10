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


function create_user($db, $username, $host = "localhost", $password = "123456")
{
	$result = $db->query("create user '$username'@'$host' identified by '$password';");
	$result2 = false;
	if($result !== false)
		$result2 = $db->query("grant all privileges on *.* to '$username'@'$host' with grant option;");

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
	$db = new mysqli(DB_HOST, 'root', $root_pass);
	$result = $db->query("select User from mysql.user where User = '" . DB_USER . "';");
	if($result === false)
		echo("couldn't check user list: " . $db->error . "\n");

	if(get_single_result($result) == DB_USER)
		$user_found = true;

	if(!$user_found)
	{
		$result = create_user($db, DB_USER, DB_HOST, DB_PASS);

		if($result == false)
			echo("couldn't create MySQL user");
	}

	$db->close();
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASS);
$db->query("set names 'utf-8';");
$db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
$db->query("SET time_zone = '+00:00';");

$result = $db->query("create database if not exists " . DB_NAME . ";");
if($result === false)
	echo("error while trying to create database: " . $db->error . "\n");

$result = $db->query("use " . DB_NAME . ";");
if($result === false)
	echo("error while trying to select database: " . $db->error . "\n");

$result = $db->query("create table if not exists playlists( ".
	"id int(11) not null, ".
	"name varchar(255) not null, ".
	"user_id int(11) not null, ".
	"public tinyint(1) not null, ".
	"avatar varchar(255) not null ".
	") engine=MyISAM auto_increment=24 default charset=utf8;");
if($result === false)
	echo("couldn't create table 'playlists': " . $db->error . "\n");

$result = $db->query("create table if not exists playlist_track ( ".
	"id int(11) not null, ".
	"playlist_id int(11) not null, ".
	"track_id int(11) not null ".
	") engine=MyISAM auto_increment=166 default charset=utf8;");
if($result === false)
	echo("couldn't create table 'playlist_track': " . $db->error . "\n");

$result = $db->query("create table if not exists tracks ( ".
	"id int(11) not null, ".
	"file_name text character set utf8 collate utf8_general_ci not null, ".
	"author_name text character set utf8 collate utf8_general_ci not null, ".
	"track_name text character set utf8 collate utf8_general_ci not null, ".
	"user_id int(11) not null, ".
	"upload_date date not null, ".
	"file_type text not null ".
	") engine=MyISAM auto_increment=826 default charset=utf8;");
if($result === false)
	echo("couldn't create table 'tracks': " . $db->error . "\n");

$result = $db->query("create table if not exists users ( ".
	"id int(11) not null, ".
	"name varchar(255) not null, ".
	"password varchar(255) not null, ".
	"salt varchar(255) not null, ".
	"fbid varchar(255) not null, ".
	"display_name varchar(255) not null, ".
	"avatar varchar(255) not null ".
	") engine=MyISAM auto_increment=6 default charset=utf8;");
if($result === false)
	echo("couldn't create table 'users': " . $db->error . "\n");


$db->query("alter table 'playlists' add primary key ('id');");
$db->query("alter table 'playlist_track' add primary key ('id');");
$db->query("alter table 'tracks' add primary key ('id'), add key 'track_id' ('id');");
$db->query("alter table 'users' add primary key ('id');");

$db->query("delete from users;");
$db->query("insert into users (name, password, display_name) VALUES".
	"('root', 'e10adc3949ba59abbe56e057f20f883e', 'Alapértelmezett felhasználó');");

$db->query("
DROP FUNCTION IF EXISTS `levenshtein`;
CREATE FUNCTION `levenshtein`(`s1` VARCHAR(255) CHARACTER SET utf8, `s2` VARCHAR(255) CHARACTER SET utf8)
	RETURNS TINYINT UNSIGNED
	NO SQL
	DETERMINISTIC
BEGIN
	DECLARE s1_len, s2_len, i, j, c, c_temp TINYINT UNSIGNED;
	-- max strlen=255 for this function
	DECLARE cv0, cv1 VARBINARY(256);
	
	-- if any param is NULL return NULL
	-- (consistent with builtin functions)
	IF (s1 + s2) IS NULL THEN
		RETURN NULL;
	END IF;
	
	SET s1_len = CHAR_LENGTH(s1),
		s2_len = CHAR_LENGTH(s2),
		cv1 = 0x00,
		j = 1,
		i = 1,
		c = 0;
	
	-- if any string is empty,
	-- distance is the length of the other one
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
");

if($result === false)
	echo("failed to add function levenshtein_ratio: " . $db->error . "\n");


$db->close();

echo("done.\n");

?>
