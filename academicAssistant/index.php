<?php

  require "config/config.php";  

  $status = "login";
  $button = "Log In";
  $statusrep = "Don't have an account? Register Now!"; 

  if(isset($_POST["status"])){
    $status = $_POST["status"];
  }

  $noAccountError = "";

  $firstnameError = "";
  $lastnameError = "";
  $usernameError = "";
  $passwordError = "";
  $emailError = "";
  $reenterpasswordError= "";

  $userid = "";
  $firstname = "";
  $lastname = "";
  $username = "";
  $password = "";
  $reenterpassword = "";
  $email = "";

  $firstnamevar = "firstname";
  $lastnamevar = "lastname";
  $emailvar = "email";
  $password2var = "password2";

  /* Change the protocol if the user already has an account */
  if($status == "login"){
    
    $button = "Log In";
    $statusrep = "Don't have an account? Register Now!"; 
    
    $firstnamevar = "firstname";
    $lastnamevar = "lastname";
    $emailvar = "email";
    $password2var = "password2";

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
      @ $conn = new mysqli($config["DB_HOST"], $config["DB_USERNAME"], $config["DB_PASSWORD"], "academicasisstant");
      $grabAccount = "SELECT userid, first_name, last_name FROM users where username ='$username' AND password ='$password'";
      $query = $conn->prepare($grabAccount);
      $query->execute();
      $query->bind_result($userid, $firstname, $lastname);
      $query->fetch();
      $query->close();

      if($userid){
        $noAccountError = "<div>Successfully logged in! Welcome, " . $firstname . " " . $lastname . "</div>";
        header("Location: homepage.php");
        exit();
      }
      else{
        $noAccountError = "<div>Invalid Account!</div>";
      }
      @ $conn->close;
    }
  }
  
  /* Change the protocol if the user does not have an account */
  if($status == "register"){
    
    $button = "Register";
    $statusrep = "Already have an account? Log In!"; 
    
    $firstnamevar = "firstnamechange";
    $lastnamevar = "lastnamechange";
    $emailvar = "emailchange";
    $password2var = "password2change";

    if(isset($_POST["save"])){
      if(!isset($_POST["firstname"]) || trim($_POST["firstname"]) == ""){
        $firstnameError = "<div>Please enter your first name!</div>";
      }
      else{
        $firstname = $_POST["firstname"];
      }
      if(!isset($_POST["lastname"]) || trim($_POST["lastname"]) == ""){
        $lastnameError = "<div>Please enter your last name!</div>";
      }
      else{
        $lastname = $_POST["lastname"];
      }
      if(!isset($_POST["username"]) || trim($_POST["username"]) == ""){
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
      if(!isset($_POST["password2"]) || trim($_POST["password2"]) == ""){
        $reenterpasswordError = "<div>Re-enter the password!</div>";
      }
      else{
        $reenterpassword = $_POST["password2"];
      }
      if(!isset($_POST["email"]) || trim ($_POST["email"]) == ""){
        $emailError = "<div>Please enter a correct email address!</div>";
      }
      else{
        $email = $_POST["email"];
      }
    }
    
    if($password != $reenterpassword){
      $passwordError = "Passwords do no match!";
    }

    if($firstnameError=="" && $lastnameError=="" && $usernameError=="" && $passwordError=="" && $reenterpasswordError=="" && $emailError==""){
      $servername = "localhost";
      $dbname = "localhost/phpmyadmin/";

      @ $conn = new mysqli($config["DB_HOST"], $config["DB_USERNAME"], $config["DB_PASSWORD"], "academicasisstant");
      if ($conn->connect_error){
        die("Error while trying to connect to database: " . $conn->connect_error);
      }

      if($firstname && $lastname && $username && $password && $email){
        
        $grabAccount = "SELECT userid, first_name, last_name FROM users where username ='$username' AND password ='$password'";
        $query = $conn->prepare($grabAccount);
        $query->execute();
        $query->bind_result($userid, $firstname, $lastname);
        $query->fetch();
        $query->close();
        
        if($userid){
          echo "Account already registered. Log In!";
        }
        
        else{
          $sql = "INSERT INTO users (last_name, first_name, username, password, email) VALUES ('$lastname', '$firstname', '$username', '$password', '$email')";

          if($conn->query($sql) === TRUE){
            echo "New record created successfully";
          }
          else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        }
      }
      @ $conn->close;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Login - Academic Assistant</title>
    <link rel="stylesheet" type="text/css" href="index.css">
  </head>
  
  <body>  
    <form id="login" name="login" action="index.php" method="post">
      
      <h1 id="logo">Login/Register</h1>
      
      <!-- When there is not an authentication error -->
      <label class="errors">
        <?php echo $noAccountError; ?>
      </label>
      
      <!-- No First name error -->
      <label class="errors">
        <?php if($firstnameError) { echo $firstnameError; } ?>
      </label>
            
      <!-- First Name input area -->
      <input class="entry" id="<?php echo $firstnamevar; ?>" type="text" size="1000" name="firstname" placeholder="First name" value="<?php echo $firstname; ?>"/>
      
      <!-- No Last name error -->
      <label class="errors">
        <?php if($lastnameError) { echo $lastnameError; } ?>
      </label>
      
      <!-- Last name input area -->
      <input class="entry" id="<?php echo $lastnamevar; ?>" type="text" size="1000" name="lastname" placeholder="Last name" value="<?php echo $lastname; ?>" />
      
      <!-- No E-mail Error -->
      <label class="errors">
        <?php if($emailError) { echo $emailError; } ?>
      </label>
      
      <!-- Email input area -->
      <input class="entry" id="<?php echo $emailvar; ?>" type="text" size="1000" name="email" placeholder="E-mail" value="<?php echo $email; ?>">
      
      <!-- Username authentication error / No username error -->
      <label class="errors">
        <?php if($usernameError){ echo $usernameError; } ?>
      </label>
      
      <!-- Username input area -->
      <input class="entry" id="username" type="text" size="1000" name="username" placeholder="Username" value="<?php echo $username; ?>"/>
    
      <!-- Password authentication error / No password error -->
      <label class="errors">
        <?php if($passwordError){ echo $passwordError; } ?>
      </label>
      
      <!-- Password input area -->
      <input class="entry" id="password" type="password" size="1000" name="password" placeholder="Password"/>
      
      <label class="errors">
        <?php if($reenterpasswordError){ echo $reenterpasswordError; } ?>
      </label>
      
      <!-- Re-entering the password -->
      <input class="entry" id="<?php echo $password2var; ?>" type="password" size="1000" name="password2" placeholder="Re-enter your password">
      
      <!-- Submit button to request authentication from database -->
      <button id="enter" type="submit" value="save" name="save"><?php echo $button; ?></button>
      
      <!-- Hidden input value that determines whether to use the login protocol or the register protocol -->
      <input id="status" type="hidden" name="status" value="<?php echo $status; ?>"/>
      
      <a id="switch">
        <?php echo $statusrep; ?>
      </a>
      
    </form>
    
  </body>
  
  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="index.js"></script>
  
</html>
