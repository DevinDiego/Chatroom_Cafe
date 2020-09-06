<?php


// ***** Declaring registration form Variables *****

$fname = "";        // first name
$lname = "";        // last name
$em = "";           // email
$em2 = "";          // confirm email
$password = "";     // password
$password2 = "";    // confirm password
$date = "";         // date
$error_array = array();  // array for holding error messages


// ==================== REGISTRATION BUTTON HANDLER ==================================

/*
  isset($_POST) — Determine if a variable is declared and is different than NULL and form method="POST" 
*/
if(isset($_POST['register_button'])) { // if click on register button. post form(register.php)


	/*          **** First Name ****       */

	// input entered in post form is now assigned to $fname
	$fname = strip_tags($_POST['reg_fname']); // Strip HTML and PHP tags from a string
	$fname = str_replace(' ', '', $fname); // remove any accidental spaces
	$fname = ucfirst(strtolower($fname)); // make first letter uppercase,rest lowercase
	$_SESSION['reg_fname'] = $fname; // Store first name in session


	/*          **** Last Name ****       */

	// input entered in post form is now assigned to $lname
	$lname = strip_tags($_POST['reg_lname']); // Strip HTML and PHP tags from a string
	$lname = str_replace(' ', '', $lname); // remove any accidental spaces
	$lname = ucfirst(strtolower($lname)); // make first letter uppercase,rest lowercase
	$_SESSION['reg_lname'] = $lname; // Store last name in session


	/*          **** Email ****       */

	// input entered in post form is now assigned to $em
	$em = strip_tags($_POST['reg_email']); // Strip HTML and PHP tags from a string
	$em = str_replace(' ', '', $em); // remove any accidental spaces
	$em = ucfirst(strtolower($em)); // make first letter uppercase,rest lowercase
	$_SESSION['reg_email'] = $em; // Store email in session



	/*          **** Confirm Email ****       */

	// input entered in post form is now assigned to $em2
	$em2 = strip_tags($_POST['reg_email2']); // Strip HTML and PHP tags from a string
	$em2 = str_replace(' ', '', $em2); // remove any accidental spaces
	$em2 = ucfirst(strtolower($em2)); // make first letter uppercase,rest lowercase
	$_SESSION['reg_email2'] = $em2; // Store confirm email in session



	/*          **** Password and Confirm Password ****       */

	// input entered in post form is now assigned to $password and $password2
	$password = strip_tags($_POST['reg_password']); // Strip HTML and PHP tags from a string
	$password2 = strip_tags($_POST['reg_password2']); // Strip HTML and PHP tags from a string


	/*          **** Date ****          */	
	$date = date("Y-m-d"); // gets current date	
	

	// Check if emails match and are in valid format
	if($em == $em2)
	{

		// filter_var() — Filters a variable with a specified filter
		if(filter_var($em, FILTER_VALIDATE_EMAIL)) 
		{ // the specified filter

			$em = filter_var($em, FILTER_VALIDATE_EMAIL);

			// Check if email already exists by querying the database users table
			$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

			// Count number of rows (results) returned from $e_check query
			$num_rows = mysqli_num_rows($e_check);

			if($num_rows > 0) 
			{
				array_push($error_array, "Email already in use!<br>");
			}

		} // END -- if(filter_var($em, FILTER_VALIDATE_EMAIL)) 

		else 
		{
			array_push($error_array, "Invalid email format!<br>");
		}

	} // END -- if($em == $em2)

	else 
	{
		array_push($error_array, "Emails do not match!<br>");
	}

	// Check length of First name
	if(strlen($fname) > 25 || strlen($fname) < 2) 
	{
		array_push($error_array, "First name must be between 2 and 25 letters!<br>");
	}

	// Check length of Last name
	if(strlen($lname) > 25 || strlen($lname) < 2) 
	{
		array_push($error_array, "Last name must be between 2 and 25 letters!<br>");
	}

	// Check if passwords match
	if($password != $password2) 
	{
		array_push($error_array, "Passwords do not match!<br>");
	}

	// preg_match() — Perform a regular expression match
	else if(preg_match('/[^A-Za-z0-9]/', $password))
	{
		array_push($error_array, "Password can only contain letters or numbers!<br>");
	}

	// Check length of password
	if(strlen($password > 30 || strlen($password) < 5))
	{
		array_push($error_array, "Password must be between 5 and 30 letters and/or numbers!<br>");
	}

	// if no errors in array
	if(empty($error_array))
	{
		// Calculates the MD5 hash of str using the » RSA Data Security, Inc. MD5 Message-Digest Algorithm, and returns that hash.
		$password = md5($password);

		// Generate username by concatenating firstname_lastname
		$username = strtolower($fname . "_" . $lname);

		// Check if username is already in database
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

		// If username already exists add a number to end of username
		// devin_ordean
		// devin_ordean1
		// devin_ordean2
		$i = 0;
		while(mysqli_num_rows($check_username_query) != 0)
		{
			$i++;
			$username = $username . $i;
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

		} // END while(mysqli_num_rows($check_username_query) != 0)

		// generate a temporary profile pic, user can change later		
		$rand = rand(1, 2); // random number between 1 and 2

		if($rand == 1)
		{
			$profile_pic = "assets/images/profile_pics/BlueHead.png";
		}
		else if($rand == 2)
		{
			$profile_pic = "assets/images/profile_pics/RedHead.png";
		}


		// Insert user inputs into users table
		// Insert random profile pic
		// Insert current date
		// num_posts, num_likes zero for now
		// user_closed = no
		// friend_array separated by comma
		$query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

		// Confirm registration is successful, proceed to login
		array_push($error_array, "<span style='color: #F10A0A;'>Registration Completed Successfully, please Login!</span><br>");

		//Clear the variables stored in the session
		$_SESSION['reg_fname'] = "";
		$_SESSION['reg_lname'] = "";
		$_SESSION['reg_email'] = "";
		$_SESSION['reg_email2'] = "";

	} // END -- if(empty($error_array))

} // END -- if(isset($_POST['register_button']))


?>