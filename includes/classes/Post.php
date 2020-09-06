<?php 

class Post {
	private $user_obj;
	private $con;

	// as described, to create user objects
	public function __construct($con, $user) {
		$this->con = $con;
		// instance of User class		
		$this->user_obj = new User($con, $user);

	} // END function __construct





	// post text body and to whom posting to
	public function submitPost($body, $user_to, $imageName) {

		// Strip HTML and PHP tags from a string
		$body = strip_tags($body);

		// Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
		$body = mysqli_real_escape_string($this->con, $body);

		// replace carriage return or space with a next line
		$body = str_replace('\r\n', '\n', $body);

		// new line to line break
		$body = nl2br($body);

		// Perform a regular expression search and replace.
		// Regular expression = remove spaces.
		$check_empty = preg_replace('/\s+/', '', $body);

		if($check_empty != "" || $imageName != "") 
		{
			$body_array = preg_split("/\s+/", $body);

			//=========================== YOUTUBE ================================

			foreach($body_array as $key => $value) 
			{
				if(strpos($value, "www.youtube.com/watch?v=") !== false) 
				{
					// https://www.youtube.com/watch?v=SfGuIVzE_Os&list=PLlrATfBNZ98dudnM48yfGUldqGD0S4FFb&index=5

					// preg_split link at & as shown below

					// https://www.youtube.com/watch?v=SfGuIVzE_Os
					// list=PLlrATfBNZ98dudnM48yfGUldqGD0S4FFb
					// index=5

					// then embed link[0]

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);



			// Current date and time
			$date_added = date("Y-m-d H:i:s");

			// Get username
			$added_by = $this->user_obj->getUserName();

			// If user is on own profile, user_to is empty
			if($user_to == $added_by) 
			{
				$user_to = "none";
			}

			// Insert post into database
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");

			// return the id at time of uploading post
			$returned_id = mysqli_insert_id($this->con);

			// Insert Notification
			if($user_to != 'none')
			{
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "like");
			}

			// Update post count for user
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");


// ================= WORDS NOT TO INCLUDE IN TRENDING SECTION ==========================


$stopWords = "a about above across after afterwards again against all almost alone along already also although always am among amongst amoungst amount an and another any anyhow anyone anything anyway anywhere are around as at b back be became because become becomes becoming been before beforehand behind being below beside besides between beyond bill both bottom but by call can cannot cant co computer con could couldnt cry de describe detail do done down due during each eg eight either eleven else elsewhere empty enough etc even ever every everyone everything everywhere except few fifteen fify fill find fire first five for former formerly forty found four from front full further get give go had has hasnt have he hence her here hereafter hereby herein hereupon hers herse him himse his how however hundred i ie if in inc indeed interest into is it its itse keep last latter latterly least less ltd made many may me meanwhile might mill mine more moreover most mostly move much must my myse name namely neither never nevertheless next nine no nobody none noone nor not nothing now nowhere of off often on once one only onto or other others otherwise our ours ourselves out over own part per perhaps please put rather re same see seem seemed seeming seems serious several she should show side since sincere six sixty so some somehow someone something sometime sometimes somewhere still such system take ten than that the their them themselves then thence there thereafter thereby therefore therein thereupon these they thick thin third this those though three through throughout thru thus to together too top toward towards twelve twenty two un under until up upon us very via was we well were what whatever when whence whenever where whereafter whereas whereby wherein whereupon wherever whether which while whither who whoever whole whom whose why will with within without would yet you your yours yourself yourselves also although always among am an and another any anybody anyone anything anywhere are area areas around as ask asked asking asks at away b back backed backing backs be became because become becomes been before began behind being beings best better between big both but by c came can cannot case cases certain certainly clear clearly come could d did differ different differently do does done down down downed downing downs during e each early either end everyone everything everywhere f face faces fact facts far felt few find finds first for four from full fully further furthered furthering furthers g gave general generally get gets give given gives go going good goods got great greater greatest group grouped grouping groups h had has have having he her here herself high high high higher highest him himself his how however i im if important in interest interested interesting interests into is it its itself j just k keep keeps kind knew know known knows large largely last later latest least less let lets like likely long longer longest m made make making man many may me member members men might more most mostly mr mrs much must my myself n necessary need needed needing needs never new new newer newest next no nobody non noone not nothing now nowhere number numbers o of off often old older oldest on once one only open opened opening opens or order ordered ordering orders other others our out over p part parted parting parts per perhaps place places point pointed pointing points possible present presented presenting presents problem problems put puts q quite r rather really right right room rooms s said same saw say says second seconds see seem seemed seeming seems sees several shall she should show showed showing shows side sides since small smaller smallest so some somebody someone something somewhere state states still still such sure t take taken than that the their them then there therefore these they thing things think thinks this those though thought thoughts three through thus to today together too took toward turn turned turning turns two u under until up upon us use used uses v very w want wanted wanting wants was way ways we well wells went were what when where whether which while who whole whose why will with within without work worked working works would x y year years yet you young younger youngest your yours z lol haha omg hey ill iframe wonder else like hate sleepy reason for some little yes bye choose";

 			//Convert stop words into array - split at white space
			$stopWords = preg_split("/[\s,]+/", $stopWords);

			//Remove all punctionation
			$no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

			//Predict whether user is posting a url. If so, do not check for trending words
			if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
				&& strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false)
			{
				//Convert users post (with punctuation removed) into array - split at white space
				$keywords = preg_split("/[\s,]+/", $no_punctuation);

				foreach($stopWords as $value) 
				{
					foreach($keywords as $key => $value2)
					{
						if(strtolower($value) == strtolower($value2))
							$keywords[$key] = "";
					}
				}

				foreach ($keywords as $value) 
				{
				    $this->calculateTrend(ucfirst($value));
				}
             } // END long if

		} // END if($check_empty != "")
	} // END function submitPost()





	public function calculateTrend($term) {
		if($term != '') 
		{
			$query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

			if(mysqli_num_rows($query) == 0)
				$insert_query = mysqli_query($this->con, "INSERT INTO trends(title,hits) VALUES('$term','1')");
			else 
				$insert_query = mysqli_query($this->con, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
		}
	} // END function calculateTrend($term)





	// return the posts from friends
	public function loadPostsFriends($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUserName();

		// if first set of posts loaded (first page)
		if($page == 1)
			$start = 0; // start = 0 element index in table (start of first page)
		else
			// ex.....  page = 4
			// 4 - 1 = 3
			// 3 * 10 = 30
			// $start = 30th element (index in table) or start of 3rd page
			$start = ($page - 1) * $limit; 

		$str = ""; 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

		// if $data_query returns > null
		if(mysqli_num_rows($data_query) > 0)
		{

			$num_iterations = 0; // number of results checked(not necessarily posted)
			$count = 1;

			// while looping through the associative array of posts table
			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];

				// Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none")
				{
					$user_to = "";

				} // END if($row['user_to'] == "none")
				else
				{
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				// Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);

				if($added_by_obj->isClosed())
				{			
					continue; // continue loop iteration
				}

				// create new user object
				$user_logged_obj = new User($this->con, $userLoggedIn);

				// Only show posts with whom you are friends with
				if($user_logged_obj->isFriend($added_by))
				{
					if($num_iterations++ < $start)
					{
						continue; // continue loop iteration
					}

					// Once ten posts have been loaded BREAK....
					if($count > $limit)
					{
						break; // exit loop
					}
					else
					{
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";
					

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

					?> 
					<script>
						// This method checks the selected elements for visibility. show() is run if an element is hidden. hide() is run if an element is visible - This creates a toggle effect. 
						// click: show comment block, click again: hide comment block
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);

						 	// targeting a tags and button
						    if (!target.is('a') && !target.is('button')) 
						    {
						        var element = document.getElementById("toggleComment<?php echo $id; ?>");
						 
						        if(element.style.display == "block")
						            element.style.display = "none";
						        else
						            element.style.display = "block";

						    } // END if (!target.is('a') && !target.is('button'))

						} // END function toggle()
					</script>
					<?php

					// display how many Comments() output by "str" below
					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);




					// ================== TIME FRAME ===================================

					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time);   // Time of post
					$end_date = new DateTime($date_time_now); // Current time
					$interval = $start_date->diff($end_date); // Difference start / end

					if ($interval->y >= 1) // 
					{
						if ($interval->y == 1)
							$time_message = $interval->y . " year ago"; 
						else
							$time_message = $interval->y . " years ago"; 			
					} // END if ($interval->y == 1) ======== YEAR / YEARS AGO ==============
					else if ($interval->m >= 1) 
					{
						if($interval->d == 0) 
						{
							$days = " ago";
						}
						else if($interval->d == 1) 
						{
							$days = $interval->d . " day ago";
						}
						else 
						{
							$days = $interval->d . " days ago";
						}
						if($interval->m == 1) {
							$time_message = $interval->m . " month ". $days;
						}
						else {
							$time_message = $interval->m . " months ". $days;
						}
					} // END else if ($interval->m >= 1) ======== MONTHS / DAYS AGO ========
					else if($interval->d >= 1) 
					{
						if($interval->d == 1) 
						{
							$time_message = "Yesterday";
						}
						else 
						{
							$time_message = $interval->d . " days ago";
						}
					} // END else if($interval->d >= 1) ======= YESTERDAY / DAYS AGO========
					else if($interval->h >= 1) 
					{
						if($interval->h == 1) 
						{
							$time_message = $interval->h . " hour ago";
						}
						else 
						{
							$time_message = $interval->h . " hours ago";
						}
					} // END else if($interval->h >= 1) ============ HOURS AGO ============
					else if($interval->i >= 1) 
					{
						if($interval->i == 1) 
						{
							$time_message = $interval->i . " minute ago";
						}
						else 
						{
							$time_message = $interval->i . " minutes ago";
						}
					} // END else if($interval->i >= 1) ============ MINUTES AGO ===========
					else 
					{
						if($interval->s < 30) 
						{
							$time_message = "Just now";
						}
						else 
						{
							$time_message = $interval->s . " seconds ago";
						}
					} // END else ============== JUST NOW / SECONDS AGO ==================


					if($imagePath != "") 
					{
						$imageDiv = "<div class='postedImage'>
										<img src='$imagePath'>
									</div>";
					}
					else 
					{
						$imageDiv = "";
					}


					// string to display the details of the post 
					// mini image / posted_by first last name / posted_to / text body
					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>

								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
							 	</div>
							 	<div class='posted_by' style='color: #ACACAC;'>
							 		<a href='$added_by'> $first_name $last_name</a> $user_to 
							 		&nbsp;&nbsp;&nbsp;&nbsp; $time_message $delete_button
						 		</div>
						 		<div id='post_body'>
						 			$body
						 			<br>
						 			$imageDiv
						 			<br><br>
					 			</div>
					 			<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>
					 	     </div>
						 	 <div class='post_comment' id='toggleComment$id' style='display:none;'>
						 	 	<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'>
						 	 	</iframe>
						 	 </div>
						 	 <hr>";
						 	 
			 	} // END if($user_logged_obj->isFriend($added_by))

			 	?>

			 	<!-- Pop-up message using bootbox Are you sure....... -->
			 	<script>
			 		$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {
								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
								if(result)
									location.reload();
							}); // END function(result)
						}); // END $('#post<?php //echo $id; ?>').on('click', function()
					}); // END $(document).ready(function()
			 	</script>


			 	<?php


			} // END while($row = mysqli_fetch_array($data))


			// if post count > 10
			if($count > $limit)
			{
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
						 <input type='hidden' class='noMorePosts' value='false'>";
			} // END if($count > $limt)
			else
			{
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show!</p>";
			}

		} // END if(mysqli_num_rows($data_query))

		echo $str;

	} // END function loadPostsFriends()






	public function loadProfilePosts($data, $limit) {

		$page = $data['page']; 
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		// if first set of posts loaded (first page)
		if($page == 1)
			$start = 0; // start = 0 element index in table (start of first page)
		else
			// ex.....  page = 4
			// 4 - 1 = 3
			// 3 * 10 = 30
			// $start = 30th element (index in table) or start of 3rd page
			$start = ($page - 1) * $limit; 

		$str = ""; 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");

		// if $data_query returns > null
		if(mysqli_num_rows($data_query) > 0)
		{

			$num_iterations = 0; // number of results checked(not necessarily posted)
			$count = 1;

			// while looping through the associative array of posts table
			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				

				// create new user object
				$user_logged_obj = new User($this->con, $userLoggedIn);

				
				
				if($num_iterations++ < $start)
				{
					continue; // continue loop iteration
				}

				// Once ten post have been loaded BREAK....
				if($count > $limit)
				{
					break; // exit loop
				}
				else
				{
					$count++;
				}

				if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X&nbsp;</button>";
				else 
					$delete_button = "";
				

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?> 
				<script>
					// This method checks the selected elements for visibility. show() is run if an element is hidden. hide() is run if an element is visible - This creates a toggle effect. 
					// click: show comment block, click again: hide comment block
					function toggle<?php echo $id; ?>(e) {

 							if( !e ) e = window.event;

							var target = $(e.target);
							if (!target.is("a") && !target.is("button")) { 
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";

					    } // END if (!target.is('a') && !target.is('button'))

					} // END function toggle()
				</script>
				<?php

				// display how many Comments() output by "str" below
				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				$comments_check_num = mysqli_num_rows($comments_check);




				// ================== TIME FRAME =======================================

				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time);   // Time of post
				$end_date = new DateTime($date_time_now); // Current time
				$interval = $start_date->diff($end_date); // Difference start / end

				if ($interval->y >= 1) // 
				{
					if ($interval->y == 1)
						$time_message = $interval->y . " year ago"; 
					else
						$time_message = $interval->y . " years ago"; 			
				} // END if ($interval->y == 1) ======== YEAR / YEARS AGO ==============
				else if ($interval->m >= 1) 
				{
					if($interval->d == 0) 
					{
						$days = " ago";
					}
					else if($interval->d == 1) 
					{
						$days = $interval->d . " day ago";
					}
					else 
					{
						$days = $interval->d . " days ago";
					}
					if($interval->m == 1) {
						$time_message = $interval->m . " month ". $days;
					}
					else {
						$time_message = $interval->m . " months ". $days;
					}
				} // END else if ($interval->m >= 1) ======== MONTHS / DAYS AGO ========
				else if($interval->d >= 1) 
				{
					if($interval->d == 1) 
					{
						$time_message = "Yesterday";
					}
					else 
					{
						$time_message = $interval->d . " days ago";
					}
				} // END else if($interval->d >= 1) ======= YESTERDAY / DAYS AGO========
				else if($interval->h >= 1) 
				{
					if($interval->h == 1) 
					{
						$time_message = $interval->h . " hour ago";
					}
					else 
					{
						$time_message = $interval->h . " hours ago";
					}
				} // END else if($interval->h >= 1) ============ HOURS AGO ============
				else if($interval->i >= 1) 
				{
					if($interval->i == 1) 
					{
						$time_message = $interval->i . " minute ago";
					}
					else 
					{
						$time_message = $interval->i . " minutes ago";
					}
				} // END else if($interval->i >= 1) ============ MINUTES AGO ===========
				else 
				{
					if($interval->s < 30) 
					{
						$time_message = "Just now";
					}
					else 
					{
						$time_message = $interval->s . " seconds ago";
					}
				} // END else ============== JUST NOW / SECONDS AGO ==================


				// string to display the details of the post 
				// mini image / posted_by first last name / posted_to / text body
				$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
						 	</div>
						 	<div class='posted_by' style='color: #ACACAC;'>
						 		<a href='$added_by'> $first_name $last_name</a>  
						 		&nbsp;&nbsp;&nbsp;&nbsp; $time_message $delete_button
					 		</div>
					 		<div id='post_body'>
					 			$body
					 			<br><br><br>
				 			</div>
				 			<div class='newsfeedPostOptions'>
								Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
							</div>
				 	     </div>
					 	 <div class='post_comment' id='toggleComment$id' style='display:none;'>
					 	 	<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'>
					 	 	</iframe>
					 	 </div>
					 	 <hr>";		 	

			 	?>

			 	<script>
			 		$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {
								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
								if(result)
									location.reload();
							}); // END function(result)
						}); // END $('#post<?php //echo $id; ?>').on('click', function()
					}); // END $(document).ready(function()
			 	</script>


			 	<?php


			} // END while($row = mysqli_fetch_array($data))

			if($count > $limit)
			{
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
						 <input type='hidden' class='noMorePosts' value='false'>";
			} // END if($count > $limt)
			else
			{
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show!</p>";
			}

		} // END if(mysqli_num_rows($data_query))

		echo $str;

	} // END function loadProfilePosts($data, $limit)
	





	public function getSinglePost($post_id) {

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

		if(mysqli_num_rows($data_query) > 0) 
		{

			$row = mysqli_fetch_array($data_query); 
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none") 
				{
					$user_to = "";
				}
				else 
				{
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) 
				{
					return;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by))
				{
					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script> 
						function toggle<?php echo $id; ?>(e) {

 							if( !e ) e = window.event;

							var target = $(e.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					// ================== TIME FRAME =======================================

					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time);   // Time of post
					$end_date = new DateTime($date_time_now); // Current time
					$interval = $start_date->diff($end_date); // Difference start / end

					if ($interval->y >= 1) // 
					{
						if ($interval->y == 1)
							$time_message = $interval->y . " year ago"; 
						else
							$time_message = $interval->y . " years ago"; 			
					} // END if ($interval->y == 1) ======== YEAR / YEARS AGO ==============
					else if ($interval->m >= 1) 
					{
						if($interval->d == 0) 
						{
							$days = " ago";
						}
						else if($interval->d == 1) 
						{
							$days = $interval->d . " day ago";
						}
						else 
						{
							$days = $interval->d . " days ago";
						}
						if($interval->m == 1) {
							$time_message = $interval->m . " month ". $days;
						}
						else {
							$time_message = $interval->m . " months ". $days;
						}
					} // END else if ($interval->m >= 1) ======== MONTHS / DAYS AGO ========
					else if($interval->d >= 1) 
					{
						if($interval->d == 1) 
						{
							$time_message = "Yesterday";
						}
						else 
						{
							$time_message = $interval->d . " days ago";
						}
					} // END else if($interval->d >= 1) ======= YESTERDAY / DAYS AGO========
					else if($interval->h >= 1) 
					{
						if($interval->h == 1) 
						{
							$time_message = $interval->h . " hour ago";
						}
						else 
						{
							$time_message = $interval->h . " hours ago";
						}
					} // END else if($interval->h >= 1) ============ HOURS AGO ============
					else if($interval->i >= 1) 
					{
						if($interval->i == 1) 
						{
							$time_message = $interval->i . " minute ago";
						}
						else 
						{
							$time_message = $interval->i . " minutes ago";
						}
					} // END else if($interval->i >= 1) ============ MINUTES AGO ===========
					else 
					{
						if($interval->s < 30) 
						{
							$time_message = "Just now";
						}
						else 
						{
							$time_message = $interval->s . " seconds ago";
						}
					} // END else ============== JUST NOW / SECONDS AGO ==================

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>
								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>
								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>
							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";

				?>
				<script>
					$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {
								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
								if(result)
									location.reload();
							});
						});
					});
				</script>


				<?php
				} // END if($user_logged_obj->isFriend($added_by))

				else 
				{
					echo "<p>You cannot see this post because you are not friends with this user.</p>";
					return;
				}
		} // END if(mysqli_num_rows($data_query) > 0) 
		else 
		{
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
					return;
		}

		echo $str;
	}


} // END CLASS POST

?> 