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

	if(isset($_REQUEST['action'])){
		$action = mysql_real_escape_string($_REQUEST["action"]);
		if($action=='getPassword'){
			$email=mysql_real_escape_string($_GET['email']);
			$password=mysql_real_escape_string($_GET['pass']);
			$result = $mysqli->query("SELECT password FROM users where email='$email'");
			if(!$result){
				$response = 'No Result';
			}else{
				$row = $result->fetch_assoc();
				$password = crypt ($password,"Gryfindor");
				if($password==$row['password']){
					$response = 'true';
				}else{
					$response = 'false';
				}
			}
		}else{
			$response = 'Breaking';
		}
		print($response);
	}else{
		
		$id = $mysqli->real_escape_string($_GET["id"]);
		$user = $mysqli->real_escape_string($_GET["user"]);
		
		$result = $mysqli->query("SELECT * FROM shopping_cart WHERE user_id='".$user."'");
		if(!$result){
			$response = "Can't use query last name because: " . $mysqli->connect_errno . ':' . $mysqli->connect_error;
		}else
		{
			$row = mysqli_fetch_assoc($result);
			$cart = $row["book_id"];
			if($cart == "0")
				$cart .= "$id";
			else
				$cart .= ",$id";
			print $cart;
			$result = $mysqli->query("UPDATE shopping_cart SET book_id='".$cart."' WHERE user_id='".$user."'");
		}

		print "success";
		print $response;
	}

?>