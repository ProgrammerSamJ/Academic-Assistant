function changeStatus() {
  if(document.getElementById("status").value == "register"){
    document.getElementById("status").value = "login";
  }
  else{
    document.getElementById("status").value = "register";
  }
}

$(document).ready( function() {
  var number = 0;
  
  $("#switch").click(function() {
    
    if(number == 0){
      $("#switch").text("I need to register");
      number = 1;
    }
    else{
      $("#switch").text("I already have an account");
      number = 0;
    }
    
    $("#firstname").slideToggle();
    $("#lastname").slideToggle();
    $("#email").slideToggle();
    $("#password2").slideToggle();
    $(".errors").slideToggle();

    $("#enter").text("Log In");
    $("#login").css({ "transition": ".6s"});
  });
});