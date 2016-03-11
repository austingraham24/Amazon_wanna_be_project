<?php

$link = new mysqli("localhost","root","","amazon_db");
if ($link->connect_errno) 
{
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}

$book_id = $_GET['id'];
$user = 1;

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

if($action=="go")
{
	$user = 1;
	$rev = $_POST["review"];
	$rev = htmlentities($link->real_escape_string($rev));
	$result = $link->query("INSERT INTO review(user_id, book_id, input) VALUES ('$user', '$book_id', '$rev')");
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

		<!-- rating system from http://prrashi.github.io/rateYo/ -->
		<link rel="stylesheet" href="jquery.rateyo.css"/>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.0.1/jquery.rateyo.min.css">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.0.1/jquery.rateyo.min.js"></script>

		<script>
			// rating help from http://prrashi.github.io/rateYo/
			$(function () {
 
  				$("#rateYo").rateYo({
    				maxValue: 5,
    				precision: 1,
    				fullStar: true
  				});
 			});

 			$(function () {
 				var book = "<?php echo $book_id; ?>";
 				var user = "<?php echo $user; ?>";
  				$("#rateYo").rateYo()
              	.on("rateyo.set", function (e, data) {
              		$.ajax({url: "ajax2.php?id="+book+"&user="+user+"&rate="+data.rating, success: function(result){}});
              	});
			});

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
		<div class="main">
			<?php
				$result = $link->query("SELECT * FROM book WHERE id='$book_id'");
				$row = $result->fetch_assoc(); 
				$title = $row["title"];
				$author = $row["author"];
				$category = $row["category"];
				$isbn = $row["isbn"];
				$summary = $row["summary"];
			?>
            <div class="container main-container">
                <h1><?php echo $title ?></h1>
                <h3>By: <?php echo $author ?></h3>
            </div>
            <div name="book info">
            	<article>
            		ISBN: <?php echo $isbn ?><br/>
            		Summary: <?php echo $summary ?><br/>
            	</article>
            </div>
            <div id="rating" class="book_rating">
            	<br/>Rate this book!
            	<div id="rateYo"></div><br/>


            </div>
            <form id="yes" method="post" <?php print "action='book.php?id=$book_id'"; ?>>
				<textarea class="form-control" rows="5" name="review" id="review"></textarea>
				<p class="help-block">Submit a review for this book!</p>
				<input type="hidden" name="action" value="go"/>
				<p><button type="submit" class="btn btn-primary btn-lg"/>Submit</p>
			</form>
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