<?php
session_start();

$link = new mysqli("localhost","root","","amazon_db");
if ($link->connect_errno) {
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

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

$email = $_SESSION["user"];
$fullname="";
$result = $link->query("SELECT first_name, last_name, id FROM users where email='$email'");
$row = $result->fetch_assoc();
$first = $row['first_name'];
$last = $row['last_name'];
$fullname = $first." ".$last;
$userID = $row['id'];


if($action=="add")
{
	$wish = $_POST["book"];
	$wish = htmlentities($link->real_escape_string($wish));
	$result = $link->query("SELECT * FROM wish_list WHERE user_id='".$userID."'");
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
		$result = $link->query("UPDATE wish_list SET book_id='".$cart."' WHERE user_id='".$userID."'");
	}
}

if(isset($_GET["cat"]))
	$selected = $_GET["cat"];
else
	$selected = " ";

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
			function allowDrop(ev) {
				ev.preventDefault();
			}

			function drag(ev) {
				ev.dataTransfer.setData("text", ev.target.id);
			}

			function drop(ev) {
				ev.preventDefault();
				var data = ev.dataTransfer.getData("text");
				var data2 = <?php print($userID);?>;
				$.ajax({url: "ajax.php?id="+data+"&user="+data2, success: function(data){
				}});
			}
		</script>

	</head>
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
                <li role="presentation"><a href="#"> Welcome, <?php print($fullname);?></a></li>
                <li><a href="list.php?cart=1"><span class="glyphicon glyphicon-shopping-cart" style="align:center;"></span> Cart <span class="badge nav-badge">4</span></a></li>
                <li><a href="list.php?cart=0"><span class="glyphicon glyphicon-star" style="align:center;"></span> WishList <span class="badge nav-badge">4</span></a></li>
                <li><a href="logOut.php">Log Out</a></li>

	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>
	    <!--end nav section-->

		<!--<div class="container">-->

		<div class="main">
            <div class="container main-container">
            	<div class="sidebar-module" style="margin-top:25px;float:right;">
	            <h4>Categories</h4>
	            <form name="categories" method="post" action="main.php">
	            	<?php
	            	$result = $link->query("SELECT DISTINCT category FROM book");
	            	while($row = $result->fetch_assoc()):
	            		$go = $row["category"];
	            		print "<div class='link' id='row'><a href='main.php?cat=$go'>$go</a></div>";
	            	endwhile;
	            	print "<div id='row'><a href='main.php'>All Categories</a></div>";
	            	?>
	            </form>
	            <br/><br/>
	            <div class="well" aria-hidden="true" ondrop="drop(event)" ondragover="allowDrop(event)">
	            	<p>Drag and Drop to add to cart!</p>
	            	<span class="glyphicon glyphicon-shopping-cart" style="font-size:5em; color:#595959; margin-left:25%;"></span>

	            </div>

	          </div>
                <h1>Book Shop</h1>
                <div id=list>
	        		<?php 
	        			if($selected == " ")
	        			{
	        				$result = $link->query("SELECT * FROM book");
							$i=0;
							while($row = $result->fetch_assoc())
							{
								$title = $row["title"];
								$id = $row["id"];
								$cat = $row["category"];
								$auth = $row["author"];?>
								<form name="approve" method="post">
									<div id="bookListing" style="margin-bottom:25px;">
										<div style="display:inline-block; width:95px;;">
										<img src="images/brownBook.png" id="<?php echo $row["id"] ?>" width="100%" display="inline" draggable="true" ondragstart="drag(event)">
										</div>
										<div style="display:inline-block; font-size:18px; margin-left:15px;">
											<?php print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
											print "<div id='author' name='author'>$auth</div>";
											print "<div id='category' name='category'>$cat</div>";
											print "<input type='hidden' name='book' value='$id'/>";
											print "<input type='hidden' name='action' value='add'/>";
											print "<button type='submit' class='btn btn-primary'>Add to Wishlist</button>";?>
										</div>
									</div>
								</form>
								<?php 
							}
	        			}
	        			else
	        			{
							$result = $link->query("SELECT * FROM book WHERE category='$selected'");
							$i=0;
							while($row = $result->fetch_assoc())
							{
								$title = $row["title"];
								$id = $row["id"];
								$cat = $row["category"];
								$auth = $row["author"];?>
								<form name="approve" method="post">
									<div id="bookListing" style="margin-bottom:25px;">
										<div style="display:inline-block; width:95px;;">
										<img src="images/brownBook.png" id="<?php echo $row["id"] ?>" width="100%" display="inline" draggable="true" ondragstart="drag(event)">
										</div>
										<div style="display:inline-block; font-size:18px; margin-left:15px;">
											<?php print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
											print "<div id='author' name='author'>$auth</div>";
											print "<div id='category' name='category'>$cat</div>";
											print "<input type='hidden' name='book' value='$id'/>";
											print "<input type='hidden' name='action' value='add'/>";
											print "<button type='submit' class='btn btn-primary'>Add to Wishlist</button>";?>
										</div>
									</div>
								</form>
								<?php 
							}
						}
						?>
				</div>
            </div>
        </div>

		<footer class="footer"> <!-- take from footer example-->
	      <div class="container footer-container">
	        <p>McGonagall Books: Castle Ruins, Scotland</p>
	      </div>
	    </footer>

	</body>
</html>