function logout(){
  window.location.replace("index.php");
  return true;
}

// Resize main tab view with window
$(document).ready(function() {
	$("#mainbox").css("width", $(window).width()-200 + "px");
});

$(window).resize(function() {

	$("#mainbox").css("width", $(window).width()-200 + "px");
});