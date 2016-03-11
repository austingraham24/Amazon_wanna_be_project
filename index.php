<?php

//connect to database
$link = new mysqli("localhost","root","","amazon_db");
if ($link->connect_errno) 
{
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}

print($_SESSION);

if(isset($_SESSION)){
	$email = $_SESSION["user"];
	$pass = $_SESSION[$email];
	$result = $link->query("SELECT password FROM users where email='$email'");
	print($result);
	if($password == $result[0]){
		print("yes");
	}
}

//read in text file if the book table is empty
//help with reading in text file to database from http://forums.phpfreaks.com/topic/184172-inserting-data-into-a-mysql-table-from-a-text-file-using-php/
$result = $link->query("SELECT count(title) FROM book");
$row = $result->fetch_assoc();
if($row["count(title)"] == 0)
{
	//file to string from http://php.net/manual/en/function.file-get-contents.php
	$file = file_get_contents("book_list.txt");
	$list = explode(";", $file);
	$count = count($list);
	$i=0;
	while($i<$count-1)
	{
  		$line= explode(",", $list[$i]);
  		$sql = $link->query("INSERT INTO book(isbn, title, author, category, summary) 
  			VALUES ('".trim(preg_replace('/\s+/', ' ', $line[0]))."', '".$line[1]."', '".$line[2]."', '".$line[3]."', '".$line[4]."') ");
  		$i++;
  	}
}
$action="";
if(isset($_REQUEST["action"])){
	$action = $_REQUEST["action"];
}
else{
	$action = "none";
}



if($action == "add_user")
    {
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        
        $fname = htmlentities($link->real_escape_string($fname));
        $lname = htmlentities($link->real_escape_string($lname));
        $email = htmlentities($link->real_escape_string($email));
        $password = htmlentities($link->real_escape_string($password));
        $password = crypt ($password,"Gryfindor");
        $result = $link->query("INSERT INTO users (first_name,last_name,email,password) VALUES ('$fname', '$lname', '$email', '$password')");

        $loggedIn = true;
        print($result);

        if(!$result)
            die ('Can\'t add user because: ' . $link->error);
        else{
        	print("Adding");
        	if(!isset($_SESSION)){
				session_start();
			}
			$_SESSION["user"] = $email;
			$_SESSION[$email] = $password;
            header('Location: main.php');
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>arg12c Test Blog</title>
		<!--from jquery.com-->
		<script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

		<!--local javascript files-->
		<script src="js/main.js"></script>

		<!-- local stylesheet-->
		<link href="css/main.css" rel="stylesheet" />
		<link href="css/modal.css" rel="stylesheet" />

	</head>
	<body role="document">
	    <!-- Fixed navbar--><!--taken from a bootstrap.com theme example and modified-->
	    <nav class="navbar navbar-inverse navbar-static-top">
	      <div class="container">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="navbar-brand" href="#">McGonagall Books</a>
	        </div>
	        <div id="navbar" class="navbar-collapse collapse">
	          <ul class="nav navbar-nav pull-right">
                <li role="presentation"><a class="site-login" href="#"> Log In</a></li>

	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>
	    <!--end nav section-->

		<!--<div class="container">-->
			<div class="main index-main">
            <div class="container sign-up-container">
                <div class="text main-nav">
                    <h1>McGonagall Books</h1> 
                    <h4>The Book Shop with a Particular Proclivity for Pleasant Reads</h4>
                    <p class="lead "></p>
                    <p><a id="join-button" class="btn btn-lg btn-success site-signUp" href="#" role="button">Sign Up Today!</a></p>
                </div>
            </div>
        </div>

        <div id="sign-in-modal" class="sign-in-modal">
          	<div class="sign-in-modal-container">
                <ul class="modal-switcher">
                    <li><a href="#">Sign In</a></li>
                    <li><a href="#">New Account</a></li>
                </ul>
                <div id="modal-login"> <!-- log in form -->
                	<div id="loginError" role="alert" class="alert alert-danger modal-alert alert-hide"><p>That username and password did not match our records. Please, try again.<p></div>
                    <form class="modal-form" name="signIn" onsubmit="return validateSignIn()" method="post" action="#">
                    	<input type="hidden" name="action" value="login">
                        <p class="fieldset"><label class="modal-label">Email:</label><input required class="modal-input" id="logInEmail" name="email" type="email" placeholder="E-mail"></p>
                        <p class="fieldset"><label class="modal-label">Password:</label><input required class="modal-input" id="logInPassword" name="password" type="password"  placeholder="Password"></p>
                        <p class="fieldset"><input class="modal-input" type="submit" value="Login"></p>
                    </form>
                </div>
                <div id="modal-signup"> <!-- sign up form -->
                    <form class="modal-form" name="signUp" onsubmit="return validateSignUp()" method="post" action="#">
                    	<input type="hidden" name="action" value="add_user">
                        <p class="fieldset"><label class="modal-label">First Name:</label><input required class="modal-input" id="fname" name="fname" type="text" placeholder="First Name"></p>
                        <p class="fieldset"><label class="modal-label">Last Name:</label><input required class="modal-input" id="lname" name="lname" type="text" placeholder="Last Name"></p>
                        <p class="fieldset"><label class="modal-label">Email:</label><input required class="modal-input" id="email" name="email" type="email" placeholder="E-mail"></p>
                        <p class="fieldset"><label class="modal-label">Password:</label><input required class="modal-input" id="password" name="password" type="password"  placeholder="Password"></p>
                        <p class="fieldset"><label id="pass2Label" class="modal-label">Enter Password Again:</label><input required class="modal-input" id="password2" name="password2" type="password"  placeholder="Retype Password"></p>
                        <input class="modal-input" type="submit" value="Create Account">
                    </form>
                </div>
            </div>
          </div>

		<footer class="footer"> <!-- take from footer example-->
	      <div class="container footer-container">
	        <p>McGonagall Books: Castle Ruins, Scotland</p>
	      </div>
	    </footer>

	    <script>

	    </script>

	</body>
</html>