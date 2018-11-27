<?php

  require "config/config.php";  

  $status = "";

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
  
  /* Change the protocol if the user does not have an account */
  if($status == "register"){

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

      //----->>>>> Possibility of creating a database in the MySQL in the //----->>>>> case it does not already exists
      /////////////////////////////////////////////////////////////////////
      //  $sql = "CREATE DATABASE IF NOT EXISTS users";
      //  if($conn->query($sql) == TRUE){
      //    echo "Database created successfully";
      //  }
      //  else{
      //    echo "Error in creating database: " . $conn->error;
      //  }
      //    
      //  mysql_select_db($conn, "academicasisstant");
      //  $sql = "CREATE TABLE IF NOT EXISTS users (
      //          userid int(255) UNSIGNED NOT NULL,
      //          last_name varchar(100) NOT NULL,
      //          first_name varchar(100) NOT NULL,
      //          username varchar(100) NOT NULL,
      //          password varchar(100) NOT NULL,
      //          email varchar (320) NOT NULL
      //        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      //
      ///////////////////////////////////////////////////////////////////

      if($firstname && $lastname && $username && $password && $email){
        
        $grabAccount = "SELECT userid, first_name, last_name FROM users where username ='$username' AND password ='$password'";
        $query = $conn->prepare($grabAccount);
        $query->execute();
        $query->bind_result($userid, $firstname, $lastname);
        $query->fetch();
        $query->close();
        
        if($userid){
          echo "Account already registered. Log In!";
          echo "Running....";
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
  

  /* Change the protocol if the user already has an account */
  if($status == "login"){

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
        header("Location: index.php");
        exit();
      }
      else{
        $noAccountError = "<div>Account could not be found. Have you registered?</div>";
      }
    }
    @ $conn->close;
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Login - Academic Assistant</title>
    <link rel="stylesheet" type="text/css" href="registerlogin.css">
  </head>
  
  <body>  
    <form id="login" name="login" action="registerlogin.php" method="post">
      
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
      <input class="entry" id="firstname" type="text" size="1000" name="firstname" placeholder="First name" value="<?php echo $firstname; ?>"/>
      
      <!-- No Last name error -->
      <label class="errors">
        <?php if($lastnameError) { echo $lastnameError; } ?>
      </label>
      
      <!-- Last name input area -->
      <input class="entry" id="lastname" type="text" size="1000" name="lastname" placeholder="Last name" value="<?php echo $lastname; ?>" />
      
      <!-- No E-mail Error -->
      <label class="errors">
        <?php if($emailError) { echo $emailError; } ?>
      </label>
      
      <!-- Email input area -->
      <input class="entry" id="email" type="text" size="1000" name="email" placeholder="E-mail" value="<?php echo $email; ?>">
      
      <!-- Username authentication error / No username error -->
      <label class="errors">
        <?php if($usernameError){ echo $usernameError; } ?>
      </label>
      
      <!-- Username input area -->
      <input class="entry" id="username" type="text" size="1000" name="username" placeholder="Username" value="<?php if($status == "register"){echo $username;} ?>"/>
    
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
      <input class="entry" id="password2" type="password" size="1000" name="password2" placeholder="Re-enter your password">
      
      <!-- Submit button to request authentication from database -->
      <button id="enter" type="submit" value="save" name="save">Log In</button>
      
      <!-- Hidden input value that determines whether to use the login protocol or the register protocol -->
      <input id="status" type="hidden" name="status" value="login"/>
      
      <a id="switch">
        Don't have an account? Register Now!
      </a>
      
    </form>
    
  </body>
  
  <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="registerlogin.js"></script>
  
</html>
