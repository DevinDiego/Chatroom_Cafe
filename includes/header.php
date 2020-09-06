<?php
// REQUIRES / INCLUDES==================
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");
// END REQUIRES / INCLUDES===============


// ============= ASSIGN $user TO CURRENT USER SIGNED IN =============================

if(isset($_SESSION['username'])) 
{
	// if username in session
	$userLoggedIn = $_SESSION['username'];

	// Assign the user query
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");

	// Fetch and assign user to $user
	//----- this $user variable can be used anywhere that includes the header file ------
	$user = mysqli_fetch_array($user_details_query);
}

else
{
	// else if not isset, redirect user to register / login
	header("Location: register.php");
}

?>

<!-- ============= END ASSIGN $user TO CURRENT USER SIGNED IN =========================== -->


<html>
<head>

	<!--=========== TITLE ============-->
	<title>Welcome to Chatroom Caffe</title>
	

    <!--============================ JAVASCRIPT LIBRARIES ==============================-->
	
	<!-- JQUERY LIBRARY -- https://developers.google.com/speed/libraries/#jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<!-- Bootstrap v4.5.0 (https://getbootstrap.com/ -->
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
	<!-- custom js file -->
	<script src="assets/js/chatroomcaffe.js"></script>
	<!-- fontawesome icons for navigation bar -->
	<script src="https://kit.fontawesome.com/27d99801b9.js" crossorigin="anonymous"></script>
	<!-- for uploading image -->
	<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />	
	<script src="assets/js/jquery.Jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>

	<!--========================= END JAVASCRIPT LIBRARIES ==============================-->
	
	



    <!-- ============================== CSS ============================================-->	                        

	<!-- Bootstrap v4.5.0 (https://getbootstrap.com/ -->
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<!-- custom style sheet -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	
	<!-- for uploading image -->
	<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

	<!-- ============================== END CSS ================================-->
</head>


<body> <!-- closing </body> in (index.php) -->



<!--=================== NAVIGATION BAR FOR ALL PAGES OF WEBSITE ================-->

	<div class="top_bar"> <!-- <div one> -->
		<div class="logo"> <!-- <div two> -->
			<a href="index.php">Chatroom Caffe</a>
		</div> <!-- </div two> class logo -->



<!--================================ SEARCH FIELD  =============================-->

		<div class="search"> <!-- <div four> -->
			<form action="search.php" method="GET" name="search_form">
				<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search" autocomplete="off" id="search_text_input">

				<div class="button_holder"> <!-- <div five> -->
					<img src="assets/images/icons/search.png">
				</div> <!-- </div five> class="button_holder" -->
			</form>

			<div class="search_results"> <!-- <div six> -->
				

			</div> <!-- </div six> class="search_results" -->
			<div class="search_results_footer_empty"> <!-- <div seven> -->
				

			</div> <!-- </div seven> class="search_results_footer_empty" -->
		</div> <!-- </div four> class="search" -->


<!--================================ END SEARCH FIELD  ===========================-->


		<nav> <!-- <nav one>  -->


			<!--=================== NAVIGATION BAR ALERTS ========================-->

			<?php
				//Unread messages 
				$messages = new Message($con, $userLoggedIn);
				$num_messages = $messages->getUnreadNumber();

				//Unread notifications 
				$notifications = new Notification($con, $userLoggedIn);
				$num_notifications = $notifications->getUnreadNumber();

				//Unread friend requests 
				$user_obj = new User($con, $userLoggedIn);
				$num_requests = $user_obj->getNumberOfFriendRequests();
			?>

			<!--=================== END NAVIGATION BAR ALERTS =========================-->


			<!-- display the first name of user logged in before the icons -->			
			<a href="<?php echo $userLoggedIn; ?>">
				<?php echo "Welcome " . $user['first_name'];?></a>


			<!--======================NAVIGATION BAR ICONS ===========================-->

			<a href="index.php"><i class="fas fa-house-user"></i></a> <!-- home icon -->

			<!--=====================================================================-->

			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
				<i class="fas fa-envelope"></i>
				<?php
					if($num_messages > 0)
						echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
				?>
			</a> <!-- messages icon -->

			<!--=====================================================================-->

			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fas fa-bell"></i>
				<?php
					if($num_notifications > 0)
						echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a> <!-- notifications icon -->

			<!--=====================================================================-->

			<a href="requests.php"><i class="fas fa-users"></i>
				<?php
					if($num_requests > 0)
						echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>';
				?>
			</a> <!-- users icon -->

			<!--=====================================================================-->

			<a href="settings.php"><i class="fas fa-cog"></i></a> <!-- settings icon -->

			<!--=====================================================================-->

			<a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt"></i></a> <!-- logout icon -->


			<!--===================== END NAVIGATION BAR ICONS ===========================-->

		</nav> <!-- </nav one>  -->

		<!--============== END NAVIGATION BAR FOR ALL PAGES OF WEBSITE =================-->


		<div class="dropdown_data_window" style="height:0px; border:none;"></div>
		<input type="hidden" id="dropdown_data_type" value="">
	</div> <!-- </div one> class top_bar -->



	<!--==================== MESSAGES TAB SCROLLING FUNCTION =========================-->

	<script>
 		var userLoggedIn = '<?php echo $userLoggedIn; ?>';

 
 		 /* $(document).ready(function)
 		 Perform tasks that are needed before the user views or interacts with the page, for example to add event handlers and initialize plugins as the page's Document 
		 Object Model (DOM) becomes safe to manipulate. 
 		 */ 		 

 		$(document).ready(function() { 

 			$('.dropdown_data_window').scroll(function() {

 				// height of div containing posts (<div class="posts_area">)
 				var inner_height = $('.dropdown_data_window').innerHeight(); 
 				var scroll_top = $('.dropdown_data_window').scrollTop();
 				var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
 				var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();
 				
 				if((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') 
 				{
 					var pageName; //Holds name of page to send ajax request to
	 				var type = $('#dropdown_data_type').val();

	 				if(type == 'notification')
						pageName = "ajax_load_notifications.php";
	 				else if(type == 'message')
						pageName = "ajax_load_messages.php";

 					var ajaxReq = $.ajax({
	 				url: "includes/handlers/" + pageName,
	 				type: "POST",
	 				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
	 					$('.dropdown_data_window').find('.nextPageDropdownData').remove(); // Removes current .nextpage 
	 					$('.dropdown_data_window').find('.noMoreDropdownData').remove(); // Removes current .nextpage 
						$('.dropdown_data_window').append(response);
	 				}
	 			}); // END var ajaxReq = $.ajax

 				} // END if((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false')

 				return false; 

 			}); // END $(window).scroll(function()

 		}); // END $(document).ready(function()

 	</script>

 	<!--==================== END MESSAGES TAB SCROLLING FUNCTION =========================-->


	<div class="wrapper"> <!-- <div three> closing div in (index.php) -->
		


	


