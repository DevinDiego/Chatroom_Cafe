<?php
include("includes/header.php");

// if press cancel -> return to settings.php;
if(isset($_POST['cancel'])) 
{
	header("Location: settings.php");
}

// if press close account -> update user_closed to 'no'
if(isset($_POST['close_account'])) 
{
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location: register.php");
}


?>

<!-- **** close account header and form **** -->
<div class="main_column column">

	<h4>Close Account</h4>

	<p>Are you sure you want to close your account?</p><br><br>
	<p>Closing your account will hide your profile from other users.</p><br><br>
	<p>You can re-open your account at any time by simply logging in.</p><br><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close Account!" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Cancel" class="info settings_submit">
	</form>

</div>