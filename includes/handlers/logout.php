<?php 
/*
-----------According to https://www.w3schools.com/php/php_sessions.asp--------------
"When you work with an application, you open it, do some changes, and then you close it. This is much like a Session. The computer knows who you are. It knows when you start the application and when you end. But on the internet there is one problem: the web server does not know who you are or what you do, because the HTTP address doesn't maintain state.
Session variables solve this problem by storing user information to be used across multiple pages (e.g. first name, last name, etc). By default, session variables last until the user closes the browser.
So; Session variables hold information about one single user, and are available to all pages in one application".
*/
session_start();

/*
session_destroy() destroys all of the data associated with the current session. It does not unset any of the global variables associated with the session, or unset the session cookie. To use the session variables again, session_start() has to be called.
*/
// session_destroy(); // Clear current session for other users
session_destroy();
header("Location: ../../register.php");

?>