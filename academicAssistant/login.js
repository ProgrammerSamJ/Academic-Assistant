$(document).ready( function() {
	var state = "R"			// To track whether they're logging in or not.
							// There's definitely a better way but this
							// works for now.

	$("#switch").click(function() {
		$("#email").slideToggle();
		$("#pass2").slideToggle();
		$("#switch").slideToggle();

		$("#enter").text("Log In");
		$("#login").css({ "transition": ".6s", "padding": "0px 30px 30px 30px"});
		state = "L";
	});
});