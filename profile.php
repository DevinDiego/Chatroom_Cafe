<?php 

include("includes/header.php");

/*
Remember the .htacess file puts the username in the address bar when on profile page:

RewriteEngine On
RewriteRule ^([a-zA-Z0-9_-]+)$ profile.php?profile_username=$1
RewriteRule ^([a-zA-Z0-9_-]+)/$ profile.php?profile_username=$1
*/

$message_obj = new Message($con, $userLoggedIn);

//====================== GET NUMBER OF FRIENDS IN FRIENDS ARRAY ============================

if(isset($_GET['profile_username']))
{
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);



	// substr_count() returns the number of times the needle substring occurs in the haystack string
	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1; // minus 1 comma
}
// ==================== END GET NUMBER OF FRIENDS IN FRIENDS ARRAY ========================



//================================ SEPARATOR (READABILITY) ==================================

// if click remove friend
if(isset($_POST['remove_friend'])) 
{
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}

// if send friend request (click add)
if(isset($_POST['add_friend'])) 
{
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}

// if click respond to friend request
if(isset($_POST['respond_request'])) 
{
	header("Location: requests.php");
}

// if message sent and if message body textarea
if(isset($_POST['post_message'])) 
{
  if(isset($_POST['message_body'])) 
  {
  	// mysqli_real_escape_string -- Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
    $body = mysqli_real_escape_string($con, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }

  // redirect to messages div section in order to see the message
  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script>  		 
          $(function() {
              $('" . $link ."').tab('show');
          });
        </script>";
} // END if(isset($_POST['post_message']))



// if(isset($_POST['cancel_request'])) {
//   $user = new User($con, $userLoggedIn);
//   $user->cancelRequest($username);
// }


?>

<!--============================= SEPARATOR (READABILITY) ==================================-->


<style type="text/css">
	.wrapper {
		margin-left: 0px;
		padding-left: 0px;
	}
</style>


<!-- ============================== PROFILE PAGE LEFT ================================  -->


<div class="profile_left"> <!-- <div one> -->

	<!-- display profile pic -->
	<img src="<?php echo $user_array['profile_pic']; ?>">

	<div class="profile_info"> <!-- <div two> -->
		<!-- display number of posts -->
		<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>

		<!-- display number of likes -->
		<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>

		<!-- display number of friends -->
		<p><?php echo "Friends: " . $num_friends; ?></p>
	</div> <!-- </div two> -->

	<form action="<?php echo $username; ?>" method="POST"> <!-- <form one> -->
		<?php 
		$profile_user_obj = new User($con, $username);

		if($profile_user_obj->isClosed())
		{
			header("Location: user_closed.php");
		} // END if($profile_user_obj->isClosed()

		$logged_in_user_obj = new User($con, $userLoggedIn);

		// if the user logged in is not on own profile and is friend of other user
		if($userLoggedIn != $username)
		{
			if($logged_in_user_obj->isFriend($username))
			{
				// display Remove Friend Button
				echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';

			} // END if($logged_in_user_obj->isFriend($username))

			//  else did user logged in receive a friend request
			else if($logged_in_user_obj->didReceiveRequest($username))
			{
				echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
			}

			// if user logged in sent request, display request sent
			else if($logged_in_user_obj->didSendRequest($username))
			{
				echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
			}

			else
			{				
				echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
			}

		} // END if($userLoggedIn != $username)

		?>
		
	</form> <!-- </form one> -->

	<!-- Toggle a modal via JavaScript by clicking the button below. It will slide down and fade in from the top of the page.  (https://getbootstrap.com/docs/3.4/javascript/#modals)--> 
	<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something"><!--  Displays Post Something button profile page left -->
	
	<?php

	// Display number of mutual friends in profile_left <div one>
	// if current user is not on own profile
	if($userLoggedIn != $username)
	{
		echo '<div class="profile_info_bottom">';
		echo $logged_in_user_obj->getMutualFriends($username) . " Mutual Friends";
		echo '</div>';
	}

	?>

</div> <!-- </div one> class="profile_left" -->



<!--========================= END PROFILE LEFT =================================-->



<!--======================== PROFILE MAIN COLUMN ================================-->


<div class="profile_main_column column"> <!-- <div four>  -->

	<ul class="nav nav-tabs" role="tablist" id="profileTabs">
	  <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>      
	  <li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
	</ul>
	<div class="tab-content"> <!-- <div five> -->
		<div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div"> <!-- <div six> -->
			<!-- call to ajax / jquery script below -->
			<div class="posts_area"></div>
			<!-- loading gif to display when scrolling past 10 posts -->
			<!-- the 10 limit was set in (ajax_load_posts.php) -->
			<img id="loading" src="assets/images/icons/loading.gif">
		</div> <!-- </div six> class="tab-pane fade in active" id="newsfeed_div" -->
		<div role="tabpanel" class="tab-pane fade" id="messages_div"> <!-- <div eight> -->

			<?php			

			echo "<h4>You and <a href='" . $username ."'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
            echo "<div class='loaded_messages' id='scroll_messages'>";
            echo $message_obj->getMessages($username);
            echo "</div>";

    	    ?>

			<div class="message_post">
				<form action="" method="POST">
					<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
					<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>				
				</form>
			</div>
			<script>
				var div = document.getElementById("scroll_messages");
				div.scrollTop = div.scrollHeight;
			</script>			
		</div> <!-- </div eight> class="tab-pane fade in active" id="messages_div" -->
	</div> <!-- </div five> class="tab-content" -->
</div> <!-- </div four> -->


<!--======================== END PROFILE MAIN COLUMN ================================-->



<!-- =======================BOOTSTRAP LIVE DEMO MODAL ===============================-->


<!-- Toggle a modal via JavaScript by clicking the button below. It will slide down and fade in from the top of the page.  (https://getbootstrap.com/docs/3.4/javascript/#modals)--> 

<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="postModalLabel">Post something</h4>
        </div>
        <div class="modal-body">
          <p>This will appear on the newsfeed for your friends to see. </p>
          <form class="profile_post" action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <textarea class="form-control" name="post_body"></textarea>
              <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
              <input type="hidden" name="user_to" value="<?php echo $username; ?>">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
        </div>
      </div>
    </div>
  </div>


  <!-- ======================= END BOOTSTRAP LIVE DEMO MODAL ============================-->


  <script> // <script one>
 		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
 		var profileUsername = '<?php echo $username; ?>';

 
 		 /* $(document).ready(function)
 		 Perform tasks that are needed before the user views or interacts with the page, for example to add event handlers and initialize plugins as the page's Document 
		 Object Model (DOM) becomes safe to manipulate. 
 		 */
 		$(document).ready(function() { 
 			$('#loading').show(); // show loading gif

 			// load 10 profile page posts as defined in ajax_load_profile_posts.php
 			$.ajax({
			      url: "includes/handlers/ajax_load_profile_posts.php",
			      type: "POST",
			      data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
			      cache:false,

			      success: function(data) {
			        $('#loading').hide(); // reach 10 hide loading gif
			        $('.posts_area').html(data);
			      }
 			}); // END $.ajax({ 

 			// Scroll function to display first set of 10 posts(ajax_load_posts.php)
 			$(window).scroll(function() {

 				// get height of div containing posts (<div class="posts_area">)
 				var height = $('.posts_area').height(); 
 				var scroll_top = $(this).scrollTop();
 				var page = $('.posts_area').find('.nextPage').val();
 				var noMorePosts = $('.posts_area').find('.noMorePosts').val();

 				// if reach bottom of page show loading gif
 				if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') 
 				{
 					$('#loading').show();

 					var ajaxReq = $.ajax({
				          url: "includes/handlers/ajax_load_profile_posts.php",
				          type: "POST",
				          data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
				          cache:false,

				          success: function(response) {
				            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
				            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
				            $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

				            $('#loading').hide();
				            $('.posts_area').append(response);
				              
				          }
		 			}); // END $.ajax({ 

 				} // END if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false')

 				return false; 

 			}); // END $(window).scroll(function()
 		}); // END $(document).ready(function()

 	</script> <!-- </script one> -->
	
</div> <!-- </div three> wrapper from (header.php) -->
</body> <!-- opening <body> in (header.php) -->
</html>


