$(document).ready( function() {
  var status = "login";
  
  $("#switch").click(function() {
    
    if(status == "login"){
      $("#switch").text("Already have an account? Log In!");
      $("#enter").text("Register");
      $("#status").val("register");
      console.log($("#status").val());
      status = "register";
    }
    else{
      $("#switch").text("Dont have an account? Register Now!");
      $("#enter").text("Login");
      $("#status").val("login");
      console.log($("#status").val());
      status = "login";
    }
    
    $("#firstname").slideToggle();
    $("#lastname").slideToggle("1000", "linear");
    $("#email").slideToggle("1000", "linear");
    $("#password2").slideToggle("1000", "linear");
    $(".errors").slideToggle("1000", "linear");

  });
});