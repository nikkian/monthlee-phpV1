<?php
require("config.php"); // including necessary variables from config file (like hostname, username, pass etc)

$link;

## this function will establish connection to database server and select db to work with. If there is any error it will show error message and php will stop working, if everything goes well then it will return true
function connect_to_db() 
{
	global $dbhost, $dbname, $dbuser, $dbpass, $link;
	
	$link = mysqli_connect($dbhost,$dbuser,$dbpass);
	if($link === false)
	{
		die("Couldn't connect to db");
	}
	
	if(!mysqli_select_db($link,$dbname))
	{
		die("Couldn't select db");
	}
	
	return true;
}

function query($sql)
{
	global $link;
	$result = mysqli_query($link,$sql);
	if($result === false)
	{
		return false;
	}
	else
	{
		return $result;
	}
}

?>