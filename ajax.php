<?php

	//===========================
	//	Database connections
	//===========================	
	$mysqli = new mysqli('localhost', 'root', '', 'users'); //host, username, password, DB

	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') '
				. $mysqli->connect_error);
	}
	//===========================

	$response = "";
	
	$id = $mysqli->real_escape_string($_GET["id"]);
	$book = $mysqli->real_escape_string($_GET["book"]);
	
	$result = $mysqli->query("SELECT * FROM shopping_cart WHERE id='".$id."'");
	if(!$result)
		$response = "Can't use query last name because: " . $mysqli->connect_errno . ':' . $mysqli->connect_error;
	else
	{
		$row = mysqli_fetch_assoc($result);
		$cart = $row["cart"];
		$cart .= ",$book";
		$result = $mysqli->query("UPDATE shopping_cart SET shopping_cart='".$cart."' WHERE id='".$id."'");
	}


	print $response;

?>