<?php
include("includes/header.php"); 
?>

<div class="main_column column" id="main_column"> <!-- <div one> -->

	<h4>Friend Requests</h4>

	<?php  

	$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
	if(mysqli_num_rows($query) == 0)
		echo "You have no friend requests at this time!";
	else 
	{
		// loop through creating a new User on each iteration because the $user_from may be a different user depending on who is sending the request
		while($row = mysqli_fetch_array($query)) 
		{
			$user_from = $row['user_from'];
			$user_from_obj = new User($con, $user_from);

			echo $user_from_obj->getFirstAndLastName() . " sent you a friend request!";

			$user_from_friend_array = $user_from_obj->getFriendArray();

			// if query > 0, then respond to friend request and update database by adding friend
			if(isset($_POST['accept_request' . $user_from ])) 
			{
				$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
				$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

				// then refresh or delete the request because user_to has accepted already and re-direct to requests page
				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
				echo "You are now friends!";
				header("Location: requests.php");
			} // END if(isset($_POST['accept_request' . $user_from ])) 


			// if user_to ignores the request, just delete the appropiate fields in database and re-direct to requests page.
			if(isset($_POST['ignore_request' . $user_from ])) 
			{
				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
				echo "Request ignored!";
				header("Location: requests.php");
			} // END if(isset($_POST['ignore_request' . $user_from ]))

			?>

			<!-- form and buttons to handle the above conditions -->
			<form action="requests.php" id="requests_form" method="POST">
				<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
				<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
			</form>
			<?php


		} // END while($row = mysqli_fetch_array($query))

	} // END else

	?>

</div>