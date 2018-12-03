<?php

  require "config/config.php";

  if(!isset($_SESSION)){
    session_start();
  }

  /* Bringing over the userid, first name, and last name from the login page */
  $userid = $_SESSION["userid"];
  $firstname = $_SESSION["firstname"];
  $lastname = $_SESSION["lastname"];

  /* Creating PDO connection with database */
  $conn = new PDO("mysql:host=" . $config["DB_HOST"] . ";dbname=academia", $config["DB_USERNAME"], $config["DB_PASSWORD"]);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $addclasserrors = "";
  $insertedclass = "";

  /* Submitting a new class */
  if(isset($_POST["createClass"])){
    if($_POST["classname"] != "" && $_POST["worktype"] != "" && $_POST["gradeweight"] != ""){
      $addClass = $conn->prepare("INSERT INTO classes (userid, class, work_type, weight) VALUES (:userid, :class, :worktype, :weight)");

      $insert = $addClass->execute(array(":userid" => $userid,
                                         ":class" => $_POST["classname"],
                                         ":worktype" => $_POST["worktype"],
                                         ":weight" => $_POST["gradeweight"]));

      if($insert === TRUE){
        $insertedclass .= "Successfully inserted the class!";
      }
    }

    else{
      $addclasserrors .= "Please fill in all the fields to add a class!";
    }
  }

  /* Selecting the individual assignments for each class */
  $grabAssignments = $conn->query("SELECT class, work_type, assignment, due_date, grade FROM assignments WHERE userid='" . $userid . "'");
  $get = $grabAssignments->fetchAll();
  if($get){
    /* Current date used to compare with assignment dates */
    $currenttime = date("Y-m-d");

    $assignmentsarray = array();
    $indicator = "";

    /* Loop to create the individual assignments for each specific class and work */
    foreach($get as $row){
      if($row){
        if(!isset($assignmentsarray[$row["class"] . "," . $row["work_type"]])){
          if($currenttime < $row["due_date"]){
            $diff = abs(strtotime($row["due_date"]) - strtotime($currenttime));
            $diff = abs($diff / (60*60*24));
            if($diff >= 7){
              $indicator = "<div class='assignmentIndicator aiNeutral'></div>";
            }
            else{
              $indicator = "<div class='assignmentIndicator aiWarning'></div>";
            }
          }
          else if($currenttime > $row["due_date"]){
            $indicator = "<div class='assignmentIndicator aiLate'></div>";
          }
          $assignmentsarray += array($row["class"] . "," . $row["work_type"] =>
                "<div class='assignment'>" .
                  $indicator .
                  "<p class='assignmentTitle'>" . $row["assignment"] . "</p>
                  <button class='editButton'>edit</button>
                  <div class='assignmentInfo''>
                    <p class='dueDate'>" . $row["due_date"] . "</p>
                    <p class='assignmentGrade'>" . $row["grade"] . "</p>
                  </div>
                </div>");
        }
        else{
          $assignmentsarray[$row["work_type"] . "," . $row["class"]] .= "<div class='assignment'>" . $row["assignment"] . "," . $row["due_date"] . "," . $row["grade"] . "</div>";
        }
      }
    }
  }

  /* Grabbing all the classes and works of those classes for the user */
  $grabClasses = $conn->query("SELECT class, work_type, weight FROM classes WHERE userid ='" . $userid . "'");
  $get = $grabClasses->fetchAll();


  if($get){
    $classes = "";
    $classinfo = "";
    foreach($get as $row){
      if($row){
        if(empty($assignmentsarray[$row["class"] . "," . $row["work_type"]])){
          $classes .= "<option value='" . $row["class"] . "'>" . $row["class"] . "</option>";

          $classinfo .= "<div class='categoryBox'>
                            <div class='contentWindow categoryHeader'>
                              <p class='categoryTitle'>" . $row["work_type"] . "</p>
                              <button class='addAssignment'>+</button>
                              <div class='categoryStats'>
                                <p class='categoryWeight'>Weight: " . $row["weight"] . "</p>
                                <p class='categoryGrade'>87</p>
                              </div>
                            </div>
                            <div class='categoryMenu'>
                            </div>
                          </div>";
        }
        else{
          $classes .= "<option value='" . $row["class"] . "'>" . $row["class"] . "</option>";

          $classinfo .= "<div class='categoryBox'>
                            <div class='contentWindow categoryHeader'>
                              <p class='categoryTitle'>" . $row["work_type"] . "</p>
                              <button class='addAssignment'>+</button>
                              <div class='categoryStats'>
                                <p class='categoryWeight'>Weight: " . $row["weight"] . "</p>
                                <p class='categoryGrade'>87</p>
                              </div>
                            </div>
                            <div class='categoryMenu'>" .
                              $assignmentsarray[$row["class"] . "," . $row["work_type"]] .
                            "</div>
                          </div>";
        }
      }
    }
  }

?>

<!doctype html>

<html>
	<head>
		<title>Home - Academia</title>
		<link rel="stylesheet" type="text/css" href="homepage.css">
    <script>
      $(document).ready(function(){
          $('#classSelect').change(function(){
                var class = $(this).val();
                $.ajax({
                    url:"load_data.php",
                     method:"POST",
                    data:{class:class},
                    success:function(data){
                         $('#homeContent').html(data);
                    }
               });
          });
      });
    </script>
	</head>

	<body>
		<div id="header">
			<span id="logo">Academia</span>

			<div id="userpanel">
				<div id="user">
					<span id="username">
                      <?php
                        echo $firstname . " " . $lastname;
                      ?>
                    </span>
					<form id="logoutbox" method="post" action="index.php">
                      <input id="logout" name="logout" type="submit" value="Log Out"/>
                    </form>
				</div>

				<img src="https://i.pinimg.com/originals/44/03/c0/4403c0b61671a3348009888bbe6e9730.jpg" id="userimage" />
			</div>
		</div>

		<div id="mainbox">
			<div id="classHeader">
				<select id="classSelect">
					<option value="home">Home</option>
                      <?php
                        echo $classes;
                      ?>
				</select>
			</div>

			<div class="classContent" id="homeContent">

                <?php
                  echo $classinfo;
                ?>

				<!-- INTERFACE FOR USERS TO ADD A CLASS -->
				<div class="categoryBox">
					<div class="contentWindow" id="addClass">
						<p class="categoryTitle">Add New Class</p>
                        <p>
                          <?php
                            echo $insertedclass;
                          ?>
                        </p>
                        <p class="error">
                          <?php
                            echo $addclasserrors;
                          ?>
                        </p>
						<form id="addClass" method="post" action="homepage.php"> <!-- Form for new class data -------------------------------->
                          <div id="addClassTitle">
                              <p class="interfaceLabel">Class Name:</p>
                              <input class="uiField" id="classname" name="classname" type="text" placeholder="e.g. Algebra" />
                          </div>

                          <div id="addClassCategories">
                              <div id="addCategoriesTitle">
                                  <p class="interfaceLabel">Assignment Categories</p>
                                  <div id="addCategoryButton">+</div>
                              </div>

                              <div class="newCategory">
                                  <input class="uiField" id="worktype" name="worktype" type="text" placeholder="Category, e.g. Homework" />
                                  <input class="uiField" id="gradeweight" name="gradeweight" type="number" placeholder="Percent of total grade" />
                                  <div class="deleteCategory">X</div>
                              </div>
                          </div>
                          <input id="createClass" type="submit" name="createClass" value="addClass" />
						</form>

					</div>
				</div>

				<!-- INTERFACE FOR USERS TO CALCULATE GRADES THEY NEED -->
				<div class="categoryBox">
					<div class="contentWindow" id="calculator">
						<p class="categoryTitle">Calculator</p>

						<form>

						<div class="calculatorItem">
							<p class="interfaceLabel">Class:</p>
							<select id="calculatorClassSelector">
								<option value="class1">Class 1</option>
								<option value="class2">Class 2</option>
							</select>
						</div>

						<div class="calculatorItem">
							<p class="interfaceLabel">Category:</p>
							<select>
								<option value="cat1">Cat 1</option>
								<option value="cat2">Cat 2</option>
							</select>
						</div>

						<div class="calculatorItem">
							<p class="interfaceLabel">Remaining Assignments:</p>
							<input type="text" placeholder="0" class="uiField" />
						</div>

						<div class="calculatorItem">
							<p class="interfaceLabel">Desired Class Grade:</p>
							<input type="text" placeholder="0" class="uiField" />
						</div>

						<div class="calculatorItem">
							<button>Submit</button>
						</div>

						</form>
					</div>
				</div>
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
		<div class="contentWindow categoryHeader">
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
