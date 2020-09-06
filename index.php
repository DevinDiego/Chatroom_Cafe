<?php 

include("includes/header.php");


if(isset($_POST['post'])) 
{

	//========================== POST IMAGE =================================== 
	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if($imageName != "") 
	{
		$targetDir = "assets/images/posts/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000) 
		{
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") 
		{
			$errorMessage = "Sorry, only jpeg, jpg, and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) 
		{
			
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) 
			{
				//image uploaded okay
			}
			else 
			{
				//image did not upload
				$uploadOk = 0;
			}
		}
	} // END if($imageName != "") 


	if($uploadOk) 
	{
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], 'none', $imageName);
	}
	else 
	{
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}
	
} // END if(isset($_POST['post']))

?>

	
             <!-- **** PROFILE PIC AND DETAILS **** -->


 	<div class="user_details column"> <!-- <div one> -->

	 	<!-- dispay the profile pic using the $user variable defined in header.php -->
	 	<!-- echo $userLoggedIn to display name in address bar as defined in .htaccess -->
	 	<a href="<?php echo $userLoggedIn; ?>">
	 		<img src="<?php echo $user['profile_pic']; ?>"></a>

	 	<div class="user_details_left_right"> <!-- <div two> -->

		 	<!-- display user first and last name in profile_pic column with a link to profile page -->
		 	<!-- echo $userLoggedIn to display name in address bar as defined in .htaccess -->
		 	<a href="<?php echo $userLoggedIn;?>">
		 		<?php echo $user['first_name'] . " " . $user['last_name']; ?></a>
		 		<br><br>

		 	<!-- display num_posts in profile_pic column -->
		 	<?php echo "Posts: " . $user['num_posts']; ?><br><br>

		 	<!-- display num_likes in profile_pic column -->
		 	<?php echo "Likes: " . $user['num_likes']; ?>

	 	</div> <!-- </div two> -->

 	</div> <!-- </div one> user_details column -->

 	<div class="main_column column"> <!-- <div four> main column of post area -->



 		       <!-- **** POSTING TEXTAREA **** -->

 		<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">

 			<input type="file" name="fileToUpload" id="fileToUpload">

 			<!-- textarea for posting content -->
 			<textarea name="post_text" id="post_text" placeholder="What's New? <?php echo $user['first_name']; ?>"></textarea>

			<!-- Post button -->
			<input type="submit" name="post" id="post_button" value="Post">
			<hr>

 		</form>

 		 <!-- call to ajax / jquery script below -->
 		 <div class="posts_area"></div>

 		 <!-- loading gif to display when scrolling past 10 posts -->
 		 <!-- the 10 limit was set in (ajax_load_posts.php) -->
 		 <img id="loading" src="assets/images/icons/loading.gif">

 	</div> <!-- </div four> main_column column -->


 	<!--========================= TRENDING WORDS ==============================-->

 	<div class="user_details column">
		<h4>Popular</h4>
		<div class="trends">
			<?php 
			$query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

			foreach ($query as $row) 
			{				
				$word = $row['title'];
				$word_dot = strlen($word) >= 14 ? "..." : "";

				$trimmed_word = str_split($word, 14);
				$trimmed_word = $trimmed_word[0];

				echo "<div style'padding: 1px'>";
				echo $trimmed_word . $word_dot;
				echo "<br></div><br>";
			}

			?>
		</div>
	</div>


	<!--========================= END TRENDING WORDS ==============================-->




 	             <!-- **** AJAX / JQUERY ****  -->

 	<script>
 		var userLoggedIn = '<?php echo $userLoggedIn; ?>';

 
 		 /* $(document).ready(function)
 		 Perform tasks that are needed before the user views or interacts with the page, for example to add event handlers and initialize plugins as the page's Document 
		 Object Model (DOM) becomes safe to manipulate. 
 		 */
 		$(document).ready(function() { 
 			$('#loading').show(); // before user interaction show loading gif

 			// Original ajax request for loading first set of 10 posts(ajax_load_posts.php)
 			$.ajax({
 				url: "includes/handlers/ajax_load_posts.php",
 				type: "POST",
 				data: "page=1&userLoggedIn=" + userLoggedIn,
 				cache:false,

 				success: function(data) {
 					$('#loading').hide(); // hide loading gif when data loading is complete
 					$('.posts_area').html(data);
 				}
 			}); // END $.ajax({ 


 			$(window).scroll(function() {

 				// height of div containing posts (<div class="posts_area">)
 				var height = $('.posts_area').height(); 
 				var scroll_top = $(this).scrollTop();
 				var page = $('.posts_area').find('.nextPage').val();
 				var noMorePosts = $('.posts_area').find('.noMorePosts').val();

 				// if reach bottom of page show loading gif
 				if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') 
 				{
 					$('#loading').show();

 					var ajaxReq = $.ajax({
		 				url: "includes/handlers/ajax_load_posts.php",
		 				type: "POST",
		 				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
		 				cache:false,

		 				success: function(response) {

		 					// removes current .nextpage
		 					$('.posts_area').find('.nextPage').remove(); 					 
		 					$('.posts_area').find('.noMorePosts').remove();

		 					// hide loading gif when data loading is complete
		 					$('#loading').hide(); 
		 					$('.posts_area').append(response);
		 				}
		 			}); // END $.ajax({ 



 				} // END if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false')

 				return false; 

 			}); // END $(window).scroll(function()

 		}); // END $(document).ready(function()

 	</script> 
	
</div> <!-- </div three> wrapper from (header.php) -->
</body> <!-- opening <body> in (header.php) -->
</html>


