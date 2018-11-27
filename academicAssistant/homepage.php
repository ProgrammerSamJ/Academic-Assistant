<?php

  @ $conn = new mysqli($config["DB_HOST"], $config["DB_USERNAME"], $config["DB_PASSWORD"], "academicasisstant");

  

?>


<!doctype html>

<html>
  <head>
      <title>Home - Academic Assistant</title>
      <link rel="stylesheet" type="text/css" href="homepage.css">
  </head>

  <body>
    <div id="header">
      <span id="logo">Academic Assistant</span>

      <div id="userpanel">
        <div id="user">
          <span id="username"></span>
          <span id="logout" onclick="return logout();">Sign Out</span>
        </div>

        <img src="https://stoffe.kawaiifabric.com/images/product_images/large_img/solid-red-fabric-Robert-Kaufman-USA-Red-179485-1.JPG" id="userimage" />
      </div>
    </div>

    <div id="mainbox">
      <div id="navbar">
        <button id="hometab" class="tab">Home</button>
        <button class="tab classtab">Class 1</button>
        <button class="tab classtab">Class 2</button>
      </div>

    <div id="content">
      <div id="homecontent" class="tabcontent">
          <div class="contentheader">
              <div id="homegrades" class="grades">
                  <div class="gradebubble">

                  </div>
                  <div class="gradedetails">

                  </div>
              </div>
              <div id="homeassignments" class="assignments">

              </div>
          </div>
      </div>

      <div id="class1" class="classcontent tabcontent">
          <div class="contentheader">
              <div id="homegrades" class="grades">
                  <div class="gradebubble">

                  </div>
                  <div class="gradedetails">

                  </div>
              </div>
              <div id="homeassignments" class="assignments">

              </div>
          </div>
      </div>

      <div id="class2" class="classcontent tabcontent">
          <div class="contentheader">
              <div id="homegrades" class="grades">
                  <div class="gradebubble">

                  </div>
                  <div class="gradedetails">

                  </div>
              </div>
              <div id="homeassignments" class="assignments">

              </div>
          </div>
        </div>
      </div>
    </div>

      <div id="navigation">
          <div id="hometab">
              Home
          </div>
          <div id="className1" class="classtab">
              Class1
          </div>
          <div id="className2" class="classtab">
              Class2
          </div>
      </div>



      <div id="homeContent">

      </div>



      <div id="classContent1" class="tabContent">

      </div>



      <div id="classContent2" class="tabContent">

      </div>

      <script type="text/javascript" src="jquery.js"></script>
      <script type="text/javascript" src="homepage.js"></script>
  </body>
</html>