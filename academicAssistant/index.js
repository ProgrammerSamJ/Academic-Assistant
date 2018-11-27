$(document).ready( function() {
  
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
