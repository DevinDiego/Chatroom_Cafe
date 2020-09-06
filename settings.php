<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">

	<h4 id="account_settings"><center>Account Settings</center></h4><br>
	<?php
	echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";
	?>
	<br>
	<!-- Option to change profile pic using upload.php -->
	<div><p><a href="upload.php" id="upload">Upload new profile picture</a><p></div> 
	<br><br><br><br><br><br><br>

	<label id="update">Update Name and Email</label><br><br>

	<?php
	// query current user loggedin
	$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($user_data_query);

	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$email = $row['email'];
	?>

	<!-- create form with user loggedin details already in fields -->
	<form action="settings.php" method="POST">
		<label id="first">First Name:</label>
		<input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
		<label id="first">Last Name:</label>
		<input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
		<label id="first">Email:</label>
		<input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

		<?php echo $message; ?> <!-- settings_handler.php -->

		<!-- Button to submit changes -->
		<input type="submit" name="update_details" id="update_details" value="Update Details" class="info settings_submit"><br><br>
	</form>

	<label id="change_pass">Change Password</label><br><br>
	<!-- form to change password -->
	<form action="settings.php" method="POST">
		<label id="first">Old Password:</label>
		<input type="password" name="old_password" id="settings_input"><br>
		<label id="first">New Password:</label>
		<input type="password" name="new_password_1" id="settings_input"><br>
		<label id="first">Confirm New Password:</label>
		<input type="password" name="new_password_2" id="settings_input"><br>

		<?php echo $password_message; ?> <!-- settings_handler.php -->

		<input type="submit" name="update_password" id="update_details" value="Update Password" class="info settings_submit"><br><br><br>
	</form>

	<h4 id="close">Close Account</h4>
	<form action="settings.php" method="POST">
		<input type="submit" name="close_account" value="Close Account" class="danger settings_submit">
	</form>
</div> 