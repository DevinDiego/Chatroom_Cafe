<?php 

class User {
	private $user;
	private $con;


	// as described, to create user objects
	public function __construct($con, $user) {
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	} // END function __construct




	// return username
	public function getUserName() {
		return $this->user['username'];
	} // END function getUserName()




	public function getNumberOfFriendRequests() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
		return mysqli_num_rows($query);
	} // END function getNumberOfFriendRequests()




	// return the number of posts
	public function getNumPosts() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	} // END function getNumPosts()	




	// return first and last name
	public function getFirstAndLastName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	} // END function getFirstAndLastName()




	// return profile_pic
	public function getProfilePic() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	} // END function getProfilePic()




	// return friend in friend_array
	public function getFriendArray() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['friend_array'];
	} // END function getFriendArray()




	// return whether account is closed(true) or not closed(false)
	public function isClosed() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		if($row['user_closed'] == 'yes')		
			return true;		
		else		
			return false;		
	} // END function isClosed()

	


	// check if you have friend in friend_array table
	public function isFriend($username_to_check) {
		// the friend_array table has friends separated by commas
		$usernameComma = "," . $username_to_check . ",";
		// if current user has a friend inside the commas that match
		if((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	} // END function isFriend()




	// query whether friend_requests table holds a value passing in parameter $user_from. > 0 true <= 0 false 
	public function didReceiveRequest($user_from) {
		$user_to = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}

	} // END function didReceiveRequest($user_to)




 	// query whether friend_requests table holds a value passing in parameter $user_to. > 0 true <= 0 false
	public function didSendRequest($user_to) {
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	} // END function didSendRequest($user_from)




	// remove friend from both users friend_array
	public function removeFriend($user_to_remove) {
		$logged_in_user = $this->user['username'];

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$friend_array_username = $row['friend_array'];
		// Remove friends from both users friend_array
		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
	} // END function removeFriend($user_to_remove)




	// insert into friend_requests user_to / user_from
	public function sendRequest($user_to) {
		$user_from = $this->user['username'];
		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES (NULL, '$user_to', '$user_from')");
	} // END function sendRequest($user_to)




	// If current user clicks on someone else's profile, it will dispay the number of mutual friends both have in common.
	// explode -- split the string into array where it finds a comma
	public function getMutualFriends($user_to_check) {
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		// Nested for loop to check this user's friend array($i) against the user_to_check($j)
		foreach($user_array_explode as $i) 
		{
			foreach($user_to_check_array_explode as $j)
			{
				if($i == $j && $i != "")
				{
					$mutualFriends++;
				}
			}
		} // END foreach()
		return $mutualFriends;
	} // END function getMutualFriends($user_to_check)



} // END CLASS USER


?> 