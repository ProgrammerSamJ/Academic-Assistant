<?php

  require "config/config.php";  

  if(!isset($_SESSION)){
    session_start();
  }

  $status = "login";
  $button = "Log In";
  $statusrep = "Don't have an account? Register Now!"; 

  if(isset($_POST["status"])){
    $status = $_POST["status"];
  }

  $accountmade = "";

  $noAccountError = "";

  $firstnameError = "";
  $lastnameError = "";
  $usernameError = "";
  $passwordError = "";
  $emailError = "";
  $reenterpasswordError= "";

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
      if(!isset($_POST["password"]) || trim($_POST["password"]) == ""){
        $passwordError = "<div>Please enter a password!</div>";
      }
    }

    if(isset($_POST["username"]) && isset($_POST["password"])){
      if($_POST["username"] != "" && $_POST["password"] != ""){
        $conn = new PDO("mysql:host=" . $config["DB_HOST"] . ";dbname=academia", $config["DB_USERNAME"], $config["DB_PASSWORD"]);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $grabAccount = $conn->query("SELECT userid, first_name, last_name FROM users where username = '" . $_POST['username'] . "' AND password = '" . $_POST['password'] . "'" );
        $query = $grabAccount->fetch();

        $userid = $query["userid"];
        $firstname = $query["first_name"];
        $lastname = $query["last_name"];

        if($userid){
          $_SESSION["userid"] = $userid;
          $_SESSION["firstname"] = $firstname;
          $_SESSION["lastname"] = $lastname;
          header("Location: homepage.php");
          exit();
        }
        else{
          $noAccountError = "<div>Incorrect Username or Password!</div>";
        }
      }
      
      $conn = null;
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
      if(!isset($_POST["lastname"]) || trim($_POST["lastname"]) == ""){
        $lastnameError = "<div>Please enter your last name!</div>";
      }
      if(!isset($_POST["username"]) || trim($_POST["username"]) == ""){
        $usernameError = "<div>Please enter a username!</div>";
      }
      if(!isset($_POST["password"]) || trim($_POST["password"]) == ""){
        $passwordError = "<div>Please enter a password!</div>";
      }
      if(!isset($_POST["password2"]) || trim($_POST["password2"]) == ""){
        $reenterpasswordError = "<div>Re-enter the password!</div>";
      }
      if(!isset($_POST["email"]) || trim ($_POST["email"]) == ""){
        $emailError = "<div>Please enter a correct email address!</div>";
      }

    }
  
    if(isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password2"]) && isset($_POST["email"])){

      if($_POST["firstname"] != "" && $_POST["lastname"] != "" && $_POST["username"] != "" && $_POST["password"] != "" && $_POST["email"] != "" && $_POST["password2"] != ""){
        
        if($_POST["password"] == $_POST["password2"]){
          $conn = new PDO("mysql:host=" . $config["DB_HOST"] . ";dbname=academia", $config["DB_USERNAME"], $config["DB_PASSWORD"]);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


          $grabAccount = $conn->query("SELECT userid, first_name, last_name FROM users WHERE username = '" . $_POST["username"] . "' AND password = '" . $_POST["password"] . "'" );
          $query = $grabAccount->fetch();

          $userid = $query["userid"];
          $firstname = $query["first_name"];
          $lastname = $query["last_name"];

          if($userid){
            $accountmade = "Account already registered. Log In!";
            $_POST["firstname"] = "";
            $_POST["lastname"] = "";
            $_POST["email"] = "";
            $_POST["username"] = "";
          }

          else{
            $makeAccount = $conn->prepare("INSERT INTO users (last_name, first_name, username, password, email) VALUES (:lastname, :firstname, :username, :password, :email)");
            $diditwork = $makeAccount->execute(array(":lastname" => $_POST["lastname"],
                                                     ":firstname" => $_POST["firstname"], ":username" => $_POST["username"], ":password" => $_POST["password"], ":email" => $_POST["email"]));

            if($diditwork === TRUE){
              $accountmade = "Successfully made an account! Log in to get started!";
              $_POST["firstname"] = "";
              $_POST["lastname"] = "";
              $_POST["email"] = "";
              $_POST["username"] = "";
            }
            else {
              $accountmade = "Error occurred. Please try again later.";
            }
          }
        }
        
        else{
          $passwordError = "Passwords do no match!";
        }
      }
      $conn = null;
    }
  }

  if(isset($_POST["logout"])){
    unset($_SESSION['username']);
    unset($_SESSION['uid']);
    
    setcookie(session_name(), '', time() - 72000);
    session_destroy();
    
    $accountmade = "You have successfully logged out!";
  }
?>

<!DOCTYPE html>
<html lang="en">
<html lang="en">
  <head>
    <title>Login - Academia</title>
    <link rel="stylesheet" type="text/css" href="index.css">
  </head>
  
  <body>  
    <form id="login" name="login" action="index.php" method="post">
      
      <h1 id="logo">ACADEMIA</h1>
      
      <p>
        <?php echo $accountmade; ?>
      </p>
      
      <!-- When there is not an authentication error -->
      <label class="errors">
        <?php echo $noAccountError; ?>
      </label>
      
      <!-- No First name error -->
      <label class="errors">
        <?php if($firstnameError) { echo $firstnameError; } ?>
      </label>
            
      <!-- First Name input area -->
      <input class="entry" id="<?php echo $firstnamevar; ?>" type="text" size="1000" name="firstname" placeholder="First name" value="<?php if(isset($_POST["firstname"])){echo $_POST["firstname"];} ?>"/>
      
      <!-- No Last name error -->
      <label class="errors">
        <?php if($lastnameError) { echo $lastnameError; } ?>
      </label>
      
      <!-- Last name input area -->
      <input class="entry" id="<?php echo $lastnamevar; ?>" type="text" size="1000" name="lastname" placeholder="Last name" value="<?php if(isset($_POST["lastname"])){echo $_POST["lastname"];} ?>" />
      
      <!-- No E-mail Error -->
      <label class="errors">
        <?php if($emailError) { echo $emailError; } ?>
      </label>
      
      <!-- Email input area -->
      <input class="entry" id="<?php echo $emailvar; ?>" type="text" size="1000" name="email" placeholder="E-mail" value="<?php if(isset($_POST["email"])){echo $_POST["email"];} ?>">
      
      <!-- Username authentication error / No username error -->
      <label class="errors">
        <?php if($usernameError){ echo $usernameError; } ?>
      </label>
      
      <!-- Username input area -->
      <input class="entry" id="username" type="text" size="1000" name="username" placeholder="Username" value="<?php if(isset($_POST["username"])){echo $_POST["username"];} ?>"/>
    
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
