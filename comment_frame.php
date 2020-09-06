<!--===============================================================================-->
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!-- custom style sheet -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>

	<style type="text/css">
		* {
			font-size: 12px;
			font-family: Arial, Helvetica, sans-serif; 
		}
	</style>

	<?php
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
	include("includes/classes/Notification.php");

	// ======================== GET CURRENT USER ======================================
	if(isset($_SESSION['username'])) 
	{
		// if username in session
		$userLoggedIn = $_SESSION['username'];

		// Assign the user query
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");

		// Fetch and assign user to $user		
		$user = mysqli_fetch_array($user_details_query);
	}

	else
	{
		// else if not isset, redirect user to login
		header("Location: register.php");
	}
	//====================== END GET CURRENT USER ======================================
	?>


	<!-- This method checks the selected elements for visibility. show() is run if an element is hidden. hide() is run if an element is visible - This creates a toggle effect. 
	click: show comment block, click again: hide comment block -->
	<script>
		function toggle() {
			var element = document.getElementById("comment_section");			
			if(element.style.display == "block")
				element.style.display = "none";
			else				
				element.style.display = "block";
		} // END function toggle()
	</script>

	<?php 


	// Get id of post
	if(isset($_GET['post_id']))
	{
		$post_id = $_GET['post_id'];
	}
	// query post_id from posts table
	$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
	// fetch associative array ($user_query) and assign to $row
	$row = mysqli_fetch_array($user_query);	
	$posted_to = $row['added_by'];
	$user_to = $row['user_to'];

	// Insert comments into comments table
	if(isset($_POST['postComment' . $post_id])) 
	{
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($con, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		$insert_post = mysqli_query($con, "INSERT INTO comments VALUES (NULL, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");


		//================ DISPLAY COMMENT (notifications) IN COMMENT FRAME ===============

		if($posted_to != $userLoggedIn) 
		{
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $posted_to, "comment");
		}
		
		if($user_to != 'none' && $user_to != $userLoggedIn) 
		{
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_to, "profile_comment");
		}


		$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
		$notified_users = array();
		while($row = mysqli_fetch_array($get_commenters)) 
		{

			if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
				&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) 
			{

				$notification = new Notification($con, $userLoggedIn);
				$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

				array_push($notified_users, $row['posted_by']);
			}

		} // END while($row = mysqli_fetch_array($get_commenters)) 


		echo "<p>Comment Posted! </p>";		
		
	} // END if(isset($_POST['postComment' . $post_id]))

	?>

	<!-- Form inside the iframe. A textarea for the comment and a button to post comment -->
	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">

		<textarea name="post_body"></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">		
	</form>
	
	<?php


	//=========================== GET COMMENTS ====================================

	$get_comments = mysqli_query($con, "SELECT * FROM comments 	WHERE post_id='$post_id' ORDER BY id ASC");
	$count = mysqli_num_rows($get_comments);

	// If count > 0 (comments to display)
	if($count != 0)
	{
		while($comment = mysqli_fetch_array($get_comments))
		{
			$comment_body = $comment['post_body'];
			$posted_to = $comment['posted_to'];
			$posted_by = $comment['posted_by'];
			$date_added = $comment['date_added'];
			$removed = $comment['removed'];
			

			// ================== TIME FRAME =======================================

			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added);   // Time of post
			$end_date = new DateTime($date_time_now); // Current time
			$interval = $start_date->diff($end_date); // Difference start / end

			if ($interval->y >= 1) // 
			{
				if ($interval->y == 1)
					$time_message = $interval->y . " year ago"; 
				else
					$time_message = $interval->y . " years ago"; 			
			} // END if ($interval->y == 1) ======== YEAR / YEARS AGO ==============
			else if ($interval->m >= 1) 
			{
				if($interval->d == 0) 
				{
					$days = " ago";
				}
				else if($interval->d == 1) 
				{
					$days = $interval->d . " day ago";
				}
				else 
				{
					$days = $interval->d . " days ago";
				}
				if($interval->m == 1) {
					$time_message = $interval->m . " month ". $days;
				}
				else {
					$time_message = $interval->m . " months ". $days;
				}
			} // END else if ($interval->m >= 1) ======== MONTHS / DAYS AGO ========
			else if($interval->d >= 1) 
			{
				if($interval->d == 1) 
				{
					$time_message = "Yesterday";
				}
				else 
				{
					$time_message = $interval->d . " days ago";
				}
			} // END else if($interval->d >= 1) ======= YESTERDAY / DAYS AGO========
			else if($interval->h >= 1) 
			{
				if($interval->h == 1) 
				{
					$time_message = $interval->h . " hour ago";
				}
				else 
				{
					$time_message = $interval->h . " hours ago";
				}
			} // END else if($interval->h >= 1) ============ HOURS AGO ============
			else if($interval->i >= 1) 
			{
				if($interval->i == 1) 
				{
					$time_message = $interval->i . " minute ago";
				}
				else 
				{
					$time_message = $interval->i . " minutes ago";
				}
			} // END else if($interval->i >= 1) ============ MINUTES AGO ===========
			else 
			{
				if($interval->s < 30) 
				{
					$time_message = "Just now";
				}
				else 
				{
					$time_message = $interval->s . " seconds ago";
				}
			} // END else ============== JUST NOW / SECONDS AGO ==================


			// create new user object (posted_by) for div one section below
			$user_obj = new User($con, $posted_by);

			?>

			<!-- Where comments are to be displayed outside of the iframe(target="_parent") -->
			<div class="comment_section"> <!-- <div one> -->

				<a href="<?php echo $posted_by?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
				<a href="<?php echo $posted_by?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
				&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?> 
				<hr>
			</div> <!-- </div one> -->

			<?php

		} // END while($comment = mysqli_fetch_array($get_comments))
	 
	} // END if($count != 0)

	// No comments to show
	else
	{
		echo "<center><br><br>No Comments to show!</center>";
	}

	?>

</body>
</html>