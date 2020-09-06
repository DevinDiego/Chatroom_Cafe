<?php 

// **************  LOGIN BUTTON HANDLER *******************

// if click on login button. login form (register.php)
if(isset($_POST['login_button']))
{

	// filter_var() — Filters a variable with a specified filter
	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // the specified filter


	// store email into session
	$_SESSION['log_email'] = $email; 


	// Calculates the MD5 hash of str using the » RSA Data Security, Inc. MD5 Message-Digest Algorithm, and returns that hash.
	$password = md5($_POST['log_password']); // get and encrypt password


	// Select all email's and password's
	$check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$password'");

	// assign results to $check_login_query
	$check_login_query = mysqli_num_rows($check_database_query);


	// if email and password match 1 row after clicking login
	if($check_login_query == 1)
	{
		
		// mysqli_fetch_array — Fetch a result row as an associative, a numeric array, or both
		$row = mysqli_fetch_array($check_database_query);

		// username = user logged in.
		$username = $row['username'];


		// query any closed accounts
		$user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");


		// If user decides to reopen account. All Information is retained in database, but a closed account is not visible to other users.
		if(mysqli_num_rows($user_closed_query) == 1)
		{
			// update user_closed to 'no' from 'yes'
			$reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
		}



		// store the session of user logged in.
		$_SESSION['username'] = $username;

		// if successful login, redirect to main page (index.php)
		header("Location: index.php");

		// exit — Output a message and terminate the current script
		exit();

	} // END if($check_database_query == 1)

	else
	{
		array_push($error_array, "Invalid credentials, try again!<br>");

	}

} // END if(isset($_POST['login_button']))

 ?>