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
}

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

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
				var data2 = 1;
				$.ajax({url: "ajax.php?id="+data+"&user="+data2, success: function(result){
				}});
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
	          <a class="navbar-brand" href="index.html">McGonagall Books</a>
	        </div>
	        <div id="navbar" class="navbar-collapse collapse">
	          <ul class="nav navbar-nav pull-right">
                <li role="presentation"><a class="cd-signin" href="#0"> Welcome, ---</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-shopping-cart" style="align:center;"></span> Cart <span class="badge nav-badge">4</span></a></li>
                <li><a href="#"><span class="glyphicon glyphicon-star" style="align:center;"></span> WishList <span class="badge nav-badge">4</span></a></li>
                <li><a href="logOut.php">Log Out</a></li>

	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>
	    <!--end nav section-->

		<!--<div class="container">-->

		<a href="list.html" class="btn btn-default">
        	<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true" ondrop="drop(event)" ondragover="allowDrop(event)"></span>
        	<!-- icon from http://glyphicons.com/ -->
        </a>

		<div class="main">
            <div class="container main-container">
                <h1>Test Main Page</h1>
            </div>
        	<div id=list>
        		<?php 
					$result = $link->query("SELECT * FROM book");
					$i=0;
					while($row = $result->fetch_assoc()):
						$title = $row["title"];
						$id = $row["id"];
						$cat = $row["category"];
						$auth = $row["author"];?>
						<form name="approve" method="post">
							<p>
								<img src="images/sample.jpg" id="<?php echo $row["id"] ?>" display="inline" draggable="true" ondragstart="drag(event)"></div>
								<?php print "<div id='title' name='title'><a href='book.php?id=$id'>$title</a></div>"; 
								print "<div id='author' name='author'>$auth</div>";
								print "<div id='category' name='category'>$cat</div>";?>
							</p>
						</form>
						<?php 
						$i++;
					endwhile;
				?>
			</div>

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