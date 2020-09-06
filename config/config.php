<?php

ob_start();
/*
--------According to https://www.php.net/manual/en/function.ob-start.php ----------
"ob_start()-- will turn output buffering on. While output buffering is active no output is sent from the script (other than headers), instead the output is stored in an internal buffer".
*/


//**** Connect to Database and start session **** 
session_start();
/*
-----------According to https://www.w3schools.com/php/php_sessions.asp--------------
"When you work with an application, you open it, do some changes, and then you close it. This is much like a Session. The computer knows who you are. It knows when you start the application and when you end. But on the internet there is one problem: the web server does not know who you are or what you do, because the HTTP address doesn't maintain state.
Session variables solve this problem by storing user information to be used across multiple pages (e.g. first name, last name, etc). By default, session variables last until the user closes the browser.
So; Session variables hold information about one single user, and are available to all pages in one application".
*/

$timezone = date_default_timezone_set("America/Los_Angeles");

// mysqli_connect() --- function opens a new connection to the MySQL server.
$con = mysqli_connect("localhost", "", "", "socialmediawebsite");

// mysqli_connect_errno() --- Returns the last error code number from the last call to mysqli_connect().
if(mysqli_connect_errno()) 
{
	echo "Failed to connect: " . mysqli_connect_errno();
}

?>