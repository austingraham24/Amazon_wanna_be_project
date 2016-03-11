<?php

	//===========================
	//	Database connections
	//===========================	
	$mysqli = new mysqli('localhost', 'root', '', 'amazon_db'); //host, username, password, DB

	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') '
				. $mysqli->connect_error);
	}
	//===========================

	$response = "";
	
	$id = $mysqli->real_escape_string($_GET["id"]);
	$user = $mysqli->real_escape_string($_GET["user"]);
	$rating = $mysqli->real_escape_string($_GET["rate"]);
	
	$result = $mysqli->query("INSERT INTO rating(book_id, rating, user_id) VALUES('$id', '$rating', '$user')");
	if(!$result)
		$response = "Can't use query last name because: " . $mysqli->connect_errno . ':' . $mysqli->connect_error;

	print "success";
	print $response;

?>