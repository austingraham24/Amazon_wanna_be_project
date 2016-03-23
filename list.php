<?php
session_start();

$link = new mysqli("localhost","root","","amazon_db");
if ($link->connect_errno) 
{
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}

if(isset($_SESSION['user'])){
	$email = $_SESSION["user"];
	$password = $_SESSION[$email];
	$result = $link->query("SELECT password FROM users where email='$email'");
	$row = $result->fetch_assoc();
	if($password != $row["password"]){
		header('Location: index.php');
	}
}else{
	header('Location: index.php');
}

$email = $_SESSION["user"];
$fullname="";
$result = $link->query("SELECT first_name, last_name, id FROM users where email='$email'");
$row = $result->fetch_assoc();
$first = $row['first_name'];
$last = $row['last_name'];
$fullname = $first." ".$last;
$userID = $row['id'];

//check if wishlist or cart page, and grab user id
$page = $_GET["cart"];
$user = 1;

if($page == 1)
{
	$result = $link->query("SELECT * FROM shopping_cart WHERE user_id = $userID");
	$row = $result->fetch_assoc();
	$list = explode(",", $row["book_id"]);
}
else
{
	$result = $link->query("SELECT * FROM wish_list WHERE user_id = $userID");
	$row = $result->fetch_assoc();
	$list = explode(",", $row["book_id"]);
}

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

if($action=="addWishList")
{
	$wish = $_POST["book"];
	$wish = htmlentities($link->real_escape_string($wish));
	$result = $link->query("SELECT * FROM wish_list WHERE user_id='".$userID."'");
	if(!$result)
		$response = "Can't use query last name because: " . $link->connect_errno . ':' . $link->connect_error;
	else
	{
		$row = mysqli_fetch_assoc($result);
		if(!$row['book_id']){
			print("stuff");
			$result = $link->query("INSERT into wish_list (book_id,user_id) values ($wish, $userID)");
		}else{
			$cart = $row["book_id"];
			if($cart == "0")
				$cart = "$wish";
			else
				$cart .= ",$wish";
			$result = $link->query("UPDATE wish_list SET book_id='".$cart."' WHERE user_id='".$userID."'");
		}
	}
}

if($action=="add")
{
	$wish = $_POST["book"];
	$wish = htmlentities($link->real_escape_string($wish));
	$result = $link->query("SELECT * FROM shopping_cart WHERE user_id='".$userID."'");
	if(!$result)
		$response = "Can't use query last name because: " . $link->connect_errno . ':' . $mysqli->connect_error;
	else
	{
		$row = mysqli_fetch_assoc($result);
		$cart = $row["book_id"];
		if(!$row["book_id"]){
			$cart = "$wish";
			$result = $link->query("INSERT into shopping_cart (user_id,book_id) values($userID,$cart)");
		}
		else{
			$cart .= ",$wish";
			$result = $link->query("UPDATE shopping_cart SET book_id='".$cart."' WHERE user_id='".$userID."'");
		}
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
	          <a class="navbar-brand" href="index.php">McGonagall Books</a>
	        </div>
	        <div id="navbar" class="navbar-collapse collapse">
	          <ul class="nav navbar-nav pull-right">
                <li role="presentation"><a class="cd-signin" href="#0"> Welcome, <?php print($fullname);?></a></li>
                <li><a href="list.php?cart=1"><span class="glyphicon glyphicon-shopping-cart" style="align:center;"></span> Cart <span class="badge nav-badge">4</span></a></li>
                <li><a href="list.php?cart=0"><span class="glyphicon glyphicon-star" style="align:center;"></span> WishList <span class="badge nav-badge">4</span></a></li>
                <li><a href="logOut.php">Log Out</a></li>

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
            			?>
            			<form name='books' method='post'>
							<div id="bookListing" style="margin-bottom:25px;">
								<div style="display:inline-block; width:100px;">
								<img src="images/brownBook.png" id="<?php echo $row["id"] ?>" width="100%" style="vertical-align:bottom;" display="inline" draggable="true" ondragstart="drag(event)">
								</div>
								<div style="display:inline-block; font-size:18px; margin-left:15px; vertical-align:bottom;">
									<?php print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
									print "<div id='author' name='author'>$auth</div>";
									print "<div id='category' name='category'>$cat</div>";
									print "<input type='hidden' name='book' value='$id'/>";
									print "<input type='hidden' name='action' value='addWishList'/>";
									print "<button type='submit' class='btn btn-primary'>Add to Wishlist</button>";?>
								</div>
							</div>
						</form>
						<?php
						$i++;
					}
					 ?>
            	</div>
            	</div>
        	</div>
        	<?php
        }
        else
        { ?>
        	<div class="main">
            	<div class="container main-container">
            	    <h1>Your Wishlist</h1>
            	    <div name="books in wishlist">
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
            			?>
            			<form name='books' method='post'>
							<div id="bookListing" style="margin-bottom:25px;">
								<div style="display:inline-block; width:100px;">
								<img src="images/brownBook.png" id="<?php echo $row["id"] ?>" width="100%" style="vertical-align:bottom;" display="inline" draggable="true" ondragstart="drag(event)">
								</div>
								<div style="display:inline-block; font-size:18px; margin-left:15px; vertical-align:bottom;">
									<?php print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
									print "<div id='author' name='author'>$auth</div>";
									print "<div id='category' name='category'>$cat</div>";
									print "<input type='hidden' name='book' value='$id'/>";
									print "<input type='hidden' name='action' value='add'/>";
									print "<button type='submit' class='btn btn-primary'>Add to Cart</button>";?>
								</div>
							</div>
						</form>
						<?php
						$i++;
					}
					 ?>
            	</div>
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