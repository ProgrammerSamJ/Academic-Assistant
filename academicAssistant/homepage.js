$("#logout").click(function() {
	// Log out user and return to login screen
});




// Resize main tab view with window
$(document).ready(function() {
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});

$(window).resize(function() {
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});

$(".categoryHeader").click(function() {
	$(this).next().slideToggle();
});