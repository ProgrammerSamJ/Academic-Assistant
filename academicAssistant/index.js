$(document).ready( function() {
  
  var height = $(window).height();
	$("#login").css("margin-top", (height-$("#login").height()-100)/2 + "px");
  
  /* jQuery that makes the Login/Register combination page possible */
  $("#switch").click(function() {
    if($("#status").val() == "login"){
      $("#switch").text("Already have an account? Log In!");
      $("#enter").text("Register");
      $("#status").val("register");
      status = "register";
    }
    else{
      $("#switch").text("Dont have an account? Register Now!");
      $("#enter").text("Login");
      $("#status").val("login");
      status = "login";
    }
    
    $("#firstname").slideToggle();
    $("#lastname").slideToggle();
    $("#email").slideToggle();
    $("#password2").slideToggle();
    
    $(".errors").slideToggle();
    
    $("#firstnamechange").slideToggle();
    $("#lastnamechange").slideToggle();
    $("#emailchange").slideToggle();
    $("#password2change").slideToggle();

  });
});

/* Some resizing functions for aesthetics */
$(window).resize(function() {
	var height = $(window).height();
	$("#login").css("margin-top", (height-$("#login").height()-100)/2 + "px");
});

$("#login").resize(function() {
	var height = $(window).height();
	$("#login").css("margin-top", (height-$("#login").height()-100)/2 + "px");
});