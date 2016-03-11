<?php

$link = new mysqli("localhost","root","","amazon_db");
if ($link->connect_errno) 
{
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}

//check if wishlist or cart page, and grab user id
$page = $_GET["cart"];
$user = 1;

if($page == 1)
{
	$result = $link->query("SELECT * FROM shopping_cart WHERE user_id = $user");
	$row = $result->fetch_assoc();
	$list = explode(",", $row["book_id"]);
}
else
{
	$result = $link->query("SELECT * FROM wish_list WHERE user_id = $user");
	$row = $result->fetch_assoc();
	$list = explode(",", $row["book_id"]);
}

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

if($action=="add")
{
	$wish = $_POST["book"];
	$wish = htmlentities($link->real_escape_string($wish));
	$result = $link->query("SELECT * FROM shopping_cart WHERE user_id='".$user."'");
	if(!$result)
		$response = "Can't use query last name because: " . $mysqli->connect_errno . ':' . $mysqli->connect_error;
	else
	{
		$row = mysqli_fetch_assoc($result);
		$cart = $row["book_id"];
		if($cart == "0")
			$cart = "$wish";
		else
			$cart .= ",$wish";
		$result = $link->query("UPDATE shopping_cart SET book_id='".$cart."' WHERE user_id='".$user."'");
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

		<!-- local stylesheet-->
		<link href="css/main.css" rel="stylesheet" />

		<script>

			function add()
			{
				document.forms["add"].submit();				
			}

		</script>

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
                <li role="presentation"><a class="cd-signin" href="#0"> Log In</a></li>

	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>
	    <!--end nav section-->

		<!--<div class="container">-->
		<?php 
		if($page == 1)
		{ ?>
			<div class="main">
            	<div class="container main-container">
            	    <h1>Your Cart</h1>
            	</div>
            	<div name="books in cart">
            		<?php 
            		$count = count($list);
            		$i = 0;
            		while($i < $count)
            		{
            			$book = (int)$list[$i];
            			$result = $link->query("SELECT * FROM book WHERE id='$book' ");
            			$row = $result->fetch_assoc();
            			$id = $row["id"];
            			$title = $row["title"];
            			$auth = $row["author"];
            			$cat = $row["category"];
            			print "<form name='books' method='post'>";
							print "<p>";
								print "<img src='images/sample.jpg' id='$id' display='inline'></div>";
								print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
								print "<div id='author' name='author'>$auth</div>";
								print "<div id='category' name='category'>$cat</div>";
							print "</p>";
						print "</form>";
						$i++;
					}
					 ?>
            	</div>
        	</div>
        	<?php
        }
        else
        { ?>
        	<div class="main">
            	<div class="container main-container">
            	    <h1>Your Wishlist</h1>
            	</div>
            	<div name="books in cart">
            		<?php 
            		$count = count($list);
            		$i = 0;
            		while($i < $count)
            		{
            			$book = (int)$list[$i];
            			$result = $link->query("SELECT * FROM book WHERE id='$book' ");
            			$row = $result->fetch_assoc();
            			$id = $row["id"];
            			$title = $row["title"];
            			$auth = $row["author"];
            			$cat = $row["category"];
            			print "<form name='add' method='post' action='list.php?cart=0'>";
							print "<p>";
								print "<img src='images/sample.jpg' id='$id' display='inline'></div>";
								print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
								print "<div id='author' name='author'>$auth</div>";
								print "<div id='category' name='category'>$cat</div>";
								print "<input type='hidden' name='book' value='$id'/>";
								print "<input type='hidden' name='action' value='add'/>";
								print "<button type='submit' class='btn btn-primary'>Add to Cart</button>";
							print "</p>";
						print "</form>";
						$i++;
					}
					?>
            	</div>
        	</div> <?php
        }?>

          <!--<div class="sidebar-module">
            <h4>Archives</h4>
            <ol class="list-unstyled">
              <li><a href="#">November 2015</a></li>
              <li><a href="#">December 2015</a></li>
              <li><a href="#">January 2016</a></li>
            </ol>
          </div>
        <!--</div><!-- /.blog-sidebar -->

			</div> <!--end div row-->

		</div> <!--end div container-->

		<footer class="footer"> <!-- take from footer example-->
	      <div class="container footer-container">
	        <p>McGonagall Books: Castle Ruins, Scotland</p>
	      </div>
	    </footer>

	</body>
</html>