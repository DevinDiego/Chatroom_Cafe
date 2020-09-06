$(document).ready(function() {

	// =============References -- https://www.w3schools.com/jquery/ajax ==================


	// SEARCH FIELD RESIZE ON FOCUS (WHEN CLICKED)
	$('#search_text_input').focus(function() { // id="search_text_input" (header.php)
		if(window.matchMedia("(min-width: 800px)").matches) {
			$(this).animate({width: '200px'}, 500); // resize to 200px
		}
	});

	// SUBMIT THE FORM AFTER CLICKING SEARCH
	$('.button_holder').on('click', function() {
		// <form action="search.php" method="GET" name="search_form"> (header.php)
		document.search_form.submit();
	})

	// Post Button (profile.php / modal-footer)
	$('#submit_profile_post').click(function(){

		// <form class="profile_post" (profile.php)
		var formData = new FormData($("form.profile_post")[0]);

		$.ajax({
			// type: specifies the type of request(Get or Post)
			type: "POST",

			// url:  // specifies the URL to send the request to
			url: "includes/handlers/ajax_submit_profile_post.php",			

			/* The serialize() method creates a URL encoded text string by serializing 
			form values. You can select one or more form elements or the form element itself. 
			The serialized values can be used in the URL query string when making an AJAX request. */

			data: $('form.profile_post').serialize(), // data: Specifies data to be sent to the server

			// success:  A function to be run when the request succeeds  
			success: function(msg) {
				$("#post_form").modal('hide'); // hides the modal

				/* The location.reload() method, will reload (or refresh) an entire 
				web page after the Ajax has performed its operation, that is, extracted
				data from an xml file. */
				location.reload();
			},

			/*The error event occurs when an element encounters an error 
			(if the element is not loaded correctly).*/
			error: function() {
				alert('Failure');
			}
		}); // END $.ajax
	}); // END $('#submit_profile_post').click(function()
 }); // END $(document).ready(function()




$(document).click(function(e){
	// If not clicking the search field div or the search field id.........
	if(e.target.className != "search_results" && e.target.id != "search_text_input") 
	{

		// and you are clicking elsewhere in document(page),
		// clear or hide the search field results!
		$(".search_results").html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}
	// same logic for the dropdown windows (messages, notifications)!
	if(e.target.className != "dropdown_data_window") 
	{

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
	}
}); // END $(document).click(function(e)




function getUsers(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, 
		function(data) {
		$(".results").html(data);
	});
} // END function getUsers(value, user)




function getDropdownData(user, type) {

	if($(".dropdown_data_window").css("height") == "0px") 
	{
		var pageName;

		if(type == 'notification') 
		{
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
		}
		else if (type == 'message') 
		{
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,

			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "280px", "border" : "1px solid #DADADA"});
				$("#dropdown_data_type").val(type);
			}
		});
	} // END if($(".dropdown_data_window").css("height") == "0px") 
	else 
	{
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}
} // END function getDropdownData(user, type)





function getLiveSearchUsers(value, user) {

	/* query is assigned to value, and userLoggedIn is assigned to user 
	   using the scope resolution operator.
	   The results of above (data), are paramater of function. */
	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn:user}, function(data) {

		// <div class="search_results_footer_empty"> (header.php)
		if($(".search_results_footer_empty")[0]) 
		{
			// toggleClass = if hiding show, if showing hide
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}

		// Add data to search results (header.php)
		$('.search_results').html(data); // <div class="search_results">
		$('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

		// if empty then empty
		if(data == "") 
		{
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");
		}
	}); // END $.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn:user}, function(data)
} // END getLiveSearchUsers(value, user)
