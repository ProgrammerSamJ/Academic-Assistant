<?php
  $usernameError = "";
  $passwordError = "";

  $noAccountError = "";

  $username = "";
  $password = "";

  $userid = "";
  $firstname = "";
  $lastname = "";

  if(isset($_POST["save"])){
    if(!isset($_POST["username"]) || trim($_POST["username"] == "")){
      $usernameError = "<div>Please enter a username!</div>";
    }
    else{
      $username = $_POST["username"];
    }
    if(!isset($_POST["password"]) || trim($_POST["password"]) == ""){
      $passwordError = "<div>Please enter a password!</div>";
    }
    else{
      $password = $_POST["password"];
    }
  }
  
  if($username && $password){
    @ $conn = new mysqli("localhost", "root", "poke2468", "academicasisstant");
    $grabAccount = "SELECT userid, first_name, last_name FROM users where username ='$username' AND password ='$password'";
    $query = $conn->prepare($grabAccount);
    $query->execute();
    $query->bind_result($userid, $firstname, $lastname);
    $query->fetch();
    $query->close();
    
    if($userid){
      $noAccountError = "<div>Successfully logged in! Welcome," . $firstname . $lastname . "</div>";
    }
    else{
      $noAccountError = "<div>Account could not be found. Have you registered?</div>";
    }
  }
  
  @ $conn->close;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>Login Page</title>
    
    <style>
      .errors {
        border: red;
        color: red;
        list-style: none;
      }
    </style>
    
  </head>
  <body>
    <h1>Sample Login page to test out the PHP and SQL</h1>
      
    <form class="login" name="login" action="login.php" method="post">
      <label class="errors">
        <?php
          echo $noAccountError;
        ?>
      </label>
      
      <label class="username" for="username">Username</label>
      <label class="errors">
        <?php
          if($usernameError){
            echo $usernameError;
          }
        ?>
      </label>
      <div class="value"><input type="text" size="1000" value="" name="username" id="username"/></div>
      
      <label class="value">Password</label>
      <label class="errors">
        <?php
          if($passwordError){
            echo $passwordError;
          }
        ?>
      </label>
      <div class="password"><input type="text" size="1000" value="" name="password" id="password"/></div>
      <input type="submit" value="save" id="save" name="save"/>
    </form>
    
  </body>
</html>