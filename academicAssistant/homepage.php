<?php

  require "config/config.php";

  if(!isset($_SESSION)){
    session_start();
  }

  $conn = new mysqli($config["DB_HOST"], $config["DB_USERNAME"], $config["DB_PASSWORD"], "academicassistant");

  $userid = $_SESSION["userid"];
  $firstname = $_SESSION["firstname"];
  $lastname = $_SESSION["lastname"];

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
					<span id="username">allenz</span>
					<span id="logout">Sign Out</span>
				</div>

				<img src="https://i.pinimg.com/originals/44/03/c0/4403c0b61671a3348009888bbe6e9730.jpg" id="userimage" />
			</div>
		</div>

		<div id="mainbox">
			<div id="classHeader">
				<select id="classSelect">
					<option value="home">Home</option>
				</select>

				<div id="classGrade">
					<div id="classGradeNumber">
						<p>84</p>
					</div>

					<div id="classGradeLetter">
						<p>B</p>
					</div>
				</div>
			</div>

			<div class="classContent" id="homeContent">
				
			</div>
			<!-- Header should have UI arrow, category title, weight, current average, and + button -->
			<!-- Each item should have indicator icon, assignment title, due date, grade, and edit button.. (delete button?) -->
			<!-- Indicator icons include check mark, gray dot, yellow warning, and red warning -->
			<!--                          complete,   neutral,   <2 days left,       late      -->
		</div>

		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="homepage.js"></script>
	</body>
</html>

<!--
	<div class="categoryBox">
		<div class="categoryHeader">
			<p class="categoryTitle">Homework</p>
			<button class="addAssignment">+</button>
			<div class="categoryStats">
				<p class="categoryWeight">weight: 0.4</p>
				<p class="categoryGrade">87</p>
			</div>
		</div>

		<div class="categoryMenu">
			<div class="assignment">

			</div>
			<div class="assignment">

			</div>
			<div class="assignment">

			</div>
			<div class="assignment">

			</div>
			<div class="assignment">

			</div>
		</div>
	</div>




	Header elements
	<div id="classGrade">
		<div id="classGradeNumber">
			<p>100</p>
		</div>

		<div id="classGradeLetter">
			<p>D</p>
		</div>
	</div>
-->

<!-- Home has class grades, assignments for the next week, calculator, and add-class window -->