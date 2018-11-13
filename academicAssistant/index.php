<?php


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Boogie Assistant</title>
    <link rel="stylesheet" href="resources/css.css">
    <script type="text/javascript" src="resources/js.js"></script>
    
    <style>
      .header {
        color: blue;
      }

      .courses {
        display: inline;

      }

      .courses button {
        border-style: none;
        background-color: white;
        padding: 20px 20px 20px 20px;
        margin-bottom: -20px;
      }
      
      .courses button:hover {
        background-color: lawngreen;
      }

      .courses button:active {
        background-color: lightskyblue;
      }
      
      .courses button.active {
        background-color: gray;
      }

      .courseInfo {
        display: none;
        background-color: lightgray;
        padding: 20px 20px 20px 20px;
      }
    </style>
  </head>
  <body>
    <h1 class="header"> Boogie Assistant </h1>
    <div class="courses">
      <button class="courseTabs" onclick="openTab(event, 'home')">Home</button>
    </div>
    <div>
      <h3 class="courseInfo" id="home">This is the homepage</h3>
    </div>
  </body>
</html>

<script>
  function openTab(thisCourse, courseName){
    var courseTabs, courseInfo;
    courseInfo = document.getElementsByClassName("courseInfo");
    for(var i=0; i<courseInfo.length; i++){
      courseInfo[i].style.display = "none";
    }
    courseTabs = document.getElementsByClassName("courseTabs");
    for(var i=0; i<courseTabs.length; i++){
      courseTabs[i].className = courseTabs[i].className.replace(" active", "");
    }
    document.getElementById(courseName).style.display = "block";
    thisCourse.currentTarget.className += " active";
  }
</script>