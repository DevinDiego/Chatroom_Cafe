/*
	According to https://api.jquery.com/ready/
	The .ready() method offers a way to run JavaScript code as soon as the page's Document 
	Object Model (DOM) becomes safe to manipulate. 
	This will often be a good time to perform tasks that are needed before the 
	user views or interacts with the page, for example to add event handlers 
	and initialize plugins. 
	When multiple functions are added via successive calls to this method, 
	they run when the DOM is ready in the order in which they are added. 
	As of jQuery 3.0, jQuery ensures that an exception occuring in one handler does not 
	prevent subsequently added handlers from executing.
*/
$(document).ready(function() {

	//On click signup, hide login and show registration form
	$("#signup").click(function() {
		$("#first").slideUp("slow", function(){
			$("#second").slideDown("slow");
		});
	});

	//On click signup, hide registration and show login form
	$("#signin").click(function() {
		$("#second").slideUp("slow", function(){
			$("#first").slideDown("slow");
		});
	});
});