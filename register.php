<?php

require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';

?>

<html>
<head>
	<title>Welcome to Chatroom Caffe</title>

	<!-- link custom register form styling / login form styling (register.php) --> 
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">

	<!-- JQUERY LIBRARY -- https://developers.google.com/speed/libraries/#jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>


</head>
<body>
	<!-- =========================== LOGIN FORM =============================== -->

	<div class="wrapper"> <!-- <div one> -->		

		<div class="login_box"> <!-- <div two> -->

			<div class="login_header"> <!-- <div three> -->
				<h1>Chatroom Caffe</h1>
				Login / Sign up below...
			</div> <!-- </div three> login_header -->

				<div id="first"> <!-- <div four> -->

					<form action="register.php" method="POST">

					<!-- Login form email input -->
					<input type="email" name="log_email" placeholder="Email Address" value="<?php

						// Hold value of login email, thanks to session
						if(isset($_SESSION['log_email'])){echo $_SESSION['log_email'];}?>" required>
						<br>

					<!-- Login form password input -->
					<input type="password" name="log_password" placeholder="Password">
					<br>

					<!-- Login form button -->
					<input type="submit" name="login_button" value="Login">
					<br>	

					<!-- Display login email error message from error_array -->
					<?php if(in_array("Invalid credentials, try again!<br>", $error_array)) echo "Invalid credentials, try again!<br>"; ?>
					<div class= "site_lock">
						<a href="#" onclick="window.open('https://www.sitelock.com/verify.php?site=chathighway.com','SiteLock','width=600,height=600,left=160,top=170');" ><img class="img-responsive" alt="SiteLock" title="SiteLock" src="//shield.sitelock.com/shield/chathighway.com" /></a>
					</div>

					<a href="#" id="signup" class="signup">Need an account? Register here!</a>	
					

					</form>

					<!--=================== END login form =============================--> 

				</div> <!-- </div four> id=first -->             

            <?php

            	// Stay on registration or login page if there are errors            	
				if(isset($_POST['register_button'])) {
					echo '
					<script>
						$(document).ready(function() {
							$("#first").hide();
							$("#second").show();
						});
					</script>
					';
				} // END if(isset($_POST['register_button']))
			?>

            <div id="second"> <!-- <div five> -->


            	<!--================== REGISTRATION FORM ============================-->

            	<form action="register.php" method="POST">

					<!-- Registration form first name input -->
					<input type="text" name="reg_fname" placeholder="First Name" value="<?php

						// Hold value of first name in form, thanks to session
						if(isset($_SESSION['reg_fname'])){echo $_SESSION['reg_fname'];}?>"required>
						<br>

						<!-- Display first name error message from error_array -->
						<?php if(in_array("First name must be between 2 and 25 letters!<br>", $error_array)) echo "First name must be between 2 and 25 letters!<br>"; ?>

					<!-- Registration form last name input -->
					<input type="text" name="reg_lname" placeholder="Last Name" value="<?php 

						// Hold value of last name in form, thanks to session
						if(isset($_SESSION['reg_lname'])){echo $_SESSION['reg_lname'];}?>"required> 
						<br>

						<!-- Display last name error message from error_array -->
						<?php if(in_array("Last name must be between 2 and 25 letters!<br>", $error_array)) echo "Last name must be between 2 and 25 letters!<br>"; ?>

					<!-- Registration form email input -->
					<input type="email" name="reg_email" placeholder="Email" value="<?php 

						// Hold value of email in form, thanks to session
						if(isset($_SESSION['reg_email'])){echo $_SESSION['reg_email'];}?>"required>
						<br>

						<!-- Display email error message from error_array -->
						<?php if(in_array("Email already in use!<br>", $error_array)) echo "Email already in use!<br>"; ?>

					<!-- Registration form confirm email input -->
					<input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php 

						// Hold value of confirm email in form, thanks to session
						if(isset($_SESSION['reg_email2'])){echo $_SESSION['reg_email2'];}?>"required>
						<br>

						<!-- Display email error messages from error_array -->
						<?php if(in_array("Email already in use!<br>", $error_array)) echo "Email already in use!<br>"; 
								else if(in_array("Invalid email format!<br>", $error_array)) echo "Invalid email format!<br>"; 
									else if(in_array("Emails do not match!<br>", $error_array)) echo "Emails do not match!<br>"; ?>

					<!-- Registration form password input -->
					<input type="password" name="reg_password" placeholder="Password" required>
					<br>

					<!-- Registration form confirm password input -->
					<input type="password" name="reg_password2" placeholder="Confirm Password" required>
					<br>

					<!-- Display password error messages from error_array -->
						<?php if(in_array("Passwords do not match!<br>", $error_array)) echo "Passwords do not match!<br>"; 
								else if(in_array("Password can only contain letters or numbers!<br>", $error_array)) echo "Password can only contain letters or numbers!<br>"; 
									else if(in_array("Password must be between 5 and 30 letters and/or numbers!<br>", $error_array)) echo "Password must be between 5 and 30 letters and/or numbers!<br>"; ?>

					<!-- Registration form register button -->
					<input type="submit" name="register_button" value="Register">
					<br>

					<!-- Display the above array message -- Registration complete -->
					<?php if(in_array("<span style='color: #F10A0A;'>Registration Completed Successfully, please Login!</span><br>", $error_array)) echo "<span style='color: #F10A0A;'>Registration Completed Successfully, please Login!</span><br>";
					?>

					<div class= "site_lock">
						<a href="#" onclick="window.open('https://www.sitelock.com/verify.php?site=chathighway.com','SiteLock','width=600,height=600,left=160,top=170');" ><img class="img-responsive" alt="SiteLock" title="SiteLock" src="//shield.sitelock.com/shield/chathighway.com" /></a>
					</div>

					<a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>

				</form> <!-- close <form action="register.php" method="POST"> -->


				<!--================== END REGISTRATION FORM ==========================-->

            </div> <!-- </div five> id=second-->			

		</div> <!-- </div two> login_box -->

	</div> <!-- </div one> wrapper-->

	          

</body>
</html>