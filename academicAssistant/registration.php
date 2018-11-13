<?php
  $firstnameError = "";
  $lastnameError = "";
  $usernameError = "";
  $passwordError = "";
  $emailError = "";

  $firstname = "";
  $lastname = "";
  $username = "";
  $password = "";
  $email = "";
  
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
    if(!isset($_POST["email"]) || trim ($_POST["email"]) == ""){
      $emailError = "<div>Please enter a correct email address!</div>";
    }
    else{
      $email = $_POST["email"];
    }
  }

  if($firstnameError=="" && $lastnameError=="" && $usernameError=="" && $passwordError=="" && $emailError==""){
    $servername = "localhost";
    $dbname = "localhost/phpmyadmin/";
    
    @ $conn = new mysqli("localhost", "root", "poke2468", "academicasisstant");
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
      $sql = "INSERT INTO users (last_name, first_name, username, password, email) VALUES ('$lastname', '$firstname', '$username', '$password', '$email')";
    
      if($conn->query($sql) === TRUE){
        echo "New record created successfully";
      }
      else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
    @ $conn->close;
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>Registration Page</title>
    
    <style>
      .errors {
        border: red;
        color: red;
        list-style: none;
      }
    </style>
    
    
  </head>
  <body>
    <h1>Sample Registration page to test out the PHP and SQL</h1>
    
    <form class="register" name="register" action="registration.php" method="post">
      <label class="firstname" for="firstname">First Name</label>
      <label class="errors">
        <?php 
          if($firstnameError) {
            echo $firstnameError;
          }
        ?>
      </label>
      <div class="value"><input type="text" size="1000" name="firstname" id="firstname"/></div>
      
      <label class="lastname" for="lastname">Last Name</label>
      <label class="errors">
        <?php 
          if($lastnameError) {
            echo $lastnameError;
          }
        ?>
      </label>
      <div class="value"><input type="text" size="1000" name="lastname" id="lastname"/></div>
      
      <label class="username" for="username">Username</label>
      <label class="errors">
        <?php 
          if($usernameError) {
            echo $usernameError;
          }
        ?>
      </label>
      <div class="value"><input type="text" size="1000" name="username" id="username"/></div>
      
      
      <label class="value">Password</label>
      <label class="errors">
        <?php 
          if($passwordError) {
            echo $passwordError;
          }
        ?>
      </label>
      <div class="password"><input type="text" size="1000" name="password" id="password"/></div>
      
      <label class="value">Email Address</label>
      <label class="errors">
        <?php 
          if($emailError) {
            echo $emailError;
          }
        ?>
      </label>
      <div class="email"><input type="text" size="1000" name="email" id="email"/></div>
      <input type="submit" value="save" id="save" name="save"/>
    </form>
     
  </body>
</html>