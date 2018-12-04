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

  /* Variables to keep track of the erorrs on the page or returning successful database manipulations */
  $addclasserrors = "";
  $insertedclass = "";

  /* Submitting a new class to the database */
  if(isset($_POST["createClass"])){
    
    /* Stripping space from class */
    $removeSpace = str_split($_POST["classname"]);
    for($x=0; $x<sizeof($removeSpace); $x++){
      if($removeSpace[$x] == " "){
        $removeSpace[$x] = "";
      }
    }
    $_POST["classname"] = join("",$removeSpace);
    
    $duplicateClass = $conn->query("SELECT class FROM classes WHERE userid='" . $_SESSION["userid"] . "' AND class='" . $_POST["classname"] . "'");
    $getDup = $duplicateClass->fetch();
    
    $classexists = $getDup["class"];
    
    if($classexists){
      $addclasserrors = "Class already exists!";
    }
    
    else{
      $checkempty = 0;
      $totalweight = 0;

      for($x=0; $x<sizeof($_POST["worktype"]); $x++){
        $totalweight += $_POST["gradeweight"][$x];
        if($_POST["classname"] == "" || $_POST["worktype"][$x] == "" || $_POST["gradeweight"][$x] == ""){
          $checkempty = 1;
        }
      }
      if($checkempty == 0 && $totalweight == 100){
        /* Inserting a newly created class */
        $addClass = $conn->prepare("INSERT INTO classes (userid, class, grade) VALUES (:userid, :class, :grade)");
        $insert = $addClass->execute(array(":userid" => $userid, 
                                           ":class" => $_POST["classname"], 
                                           ":grade" => 0));

        if($insert === TRUE){
          $insertedclass .= "Successfully inserted the class!";
        }
        else{
          $addclasserrors .= "There was an error adding the class!";
        }

        for($x=0; $x<sizeof($_POST["worktype"]); $x++){
          /* Initializing the newly added class's assignment's grade to 0 */
          $addClassGrade = $conn->prepare("INSERT INTO workgrade (userid, class, work_type, weight, grade) VALUES (:userid, :class, :worktype, :weight, :grade)");
          $insert = $addClassGrade->execute(array(":userid" => $_SESSION["userid"],
                                                  ":class" => $_POST["classname"],
                                                  ":worktype" => $_POST["worktype"][$x],
                                                  ":weight" => $_POST["gradeweight"][$x],
                                                  ":grade" => 0));
          if($insert === TRUE){
            $insertedclass .= "<br> The grade for the class will be 0 until you add an assignment!";
          }
          else{
            $addclasserrors .= "There was an error creating the class!";
          }
        }
      }
      else{
        $addclasserrors .= "Invalid fields or grade weights do not meet 100%!";
      }
    }
  }

  /* Adding an assignment to the work categories */
  if(isset($_POST["nowaddAssignment"]) && isset($_POST["editstatus"])){
    if($_POST["editstatus"] == "add"){
      $checkempty = 0;

      for($x=0; $x<sizeof($_POST["assignmentClass"]); $x++){
        if($_POST["assignmentName"][$x] == "" || $_POST["due_date"][$x] == ""){
          $checkempty = 1;
        }
      }
      if($checkempty == 0){
        for($x=0; $x<sizeof($_POST["assignmentClass"]); $x++){
          if($_POST["assignmentgrade"][$x] == ""){
            $_POST["assignmentgrade"][$x] = 0;
          }
          $_POST["due_date"][$x] = date("Y-m-d", strtotime($_POST["due_date"][$x]));

          $addAssignment = $conn->prepare("INSERT INTO assignments (userid, class, work_type, assignment, due_date, grade) VALUES (:userid, :class, :work_type, :assignment, :due_date, :grade)");
          $insert = $addAssignment->execute(array(":userid" => $userid,
                                                  ":class" => $_POST["assignmentClass"][$x],
                                                  ":work_type" => $_POST["assignmentWorktype"][$x],
                                                  ":assignment" => $_POST["assignmentName"][$x],
                                                  ":due_date" => $_POST["due_date"][$x],
                                                  ":grade" => $_POST["assignmentgrade"][$x]));
          if($insert === TRUE){
            $insertedclass .= "Successfully inserted assignment(s)!";
          }
          else{
            $addclasserrors .= "There was an error inserting assignment(s)!";
          }
        }
      }
      else{
        $addclasserrors .= "Please fill in all the fields before adding assignments!";
      }
    }
    else if ($_POST["editstatus"] == "edit"){
      
    }
    else if ($_POST["editstatus"] == "delete"){
      
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
          if($currenttime <= $row["due_date"]){
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
            if($row["grade"] == 0){
              $indicator = "<div class='assignmentIndicator aiLate'></div>";
            }
            else{
              $indicator = "<div class='assignmentIndicator aiComplete'></div>";
            }
          }
          $assignmentsarray += array($row["class"] . "," . $row["work_type"] => 
                "<div class='assignment'>" .
                    $indicator .
                    "<div class='formBox'>
                        <p class='assignmentTitle'>" . $row["assignment"] . "</p>

                        <button class='editButton'>edit</button>
                        <div class='assignmentInfo'>
                            <p class='dueDate'>due:<span>" . $row["due_date"] . "</span></p>
                            <p class='assignmentGrade'>" . $row["grade"] . "</p>
                        </div>
                    </div>
                </div>");
        }
        else{
          if($currenttime <= $row["due_date"]){
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
            if($row["grade"] == 0){
              $indicator = "<div class='assignmentIndicator aiLate'></div>";
            }
            else{
              $indicator = "<div class='assignmentIndicator aiComplete'></div>";
            }
          }
          $assignmentsarray[$row["class"] . "," . $row["work_type"]] .= 
                "<div class='assignment'>" .
                    $indicator .
                    "<div class='formBox'>
                        <p class='assignmentTitle'>" . $row["assignment"] . "</p>

                        <button class='editButton'>edit</button>
                        <div class='assignmentInfo'>
                            <p class='dueDate'>due:<span>" . $row["due_date"] . "</span></p>
                            <p class='assignmentGrade'>" . $row["grade"] . "</p>
                        </div>
                    </div>
                </div>";
        }
      }
    }
  }


  /* Grabbing the classes and the grade for the user */
  $grabClasses = $conn->query("SELECT class, grade FROM classes WHERE userid ='" . $userid . "'");
  $get = $grabClasses->fetchAll();

  $classes = "";

  foreach($get as $row){
    $classes .= "<option value='" . $row["class"] . "'>" . $row["class"] . "</option>";
  }


  /* Grabbing all of the classwork of classes for the user */
  $whichClass = $conn->query("SELECT class FROM classes WHERE userid='" . $_SESSION["userid"] . "'");
  $found = $whichClass->fetchAll();

  $classinfo = "";

  foreach($found as $classrow){
    $grabClasses = $conn->query("SELECT work_type, weight, grade FROM workgrade WHERE userid ='" . $userid . "' AND class='" . $classrow["class"] . "'");
    $get = $grabClasses->fetchAll();
    
    if($get){
      
      $classinfo .= "<div class='classContent' id='" . $classrow["class"] . "'>";
      
      foreach($get as $row){
        if($row){
          
          /* Calculating the grade for each class' work category */
          /* ------------------------------------------------------ */
          $updateworkgrade = $conn->query("SELECT grade FROM assignments WHERE userid='" .$userid."' AND class='" .$classrow["class"]. "'AND work_type='" .$row["work_type"]. "'");
          $updateprocess = $updateworkgrade->fetchAll();

          $total = 0;
          $counter = 0;
          $average = 0;

          foreach($updateprocess as $assignment){
            if($assignment["grade"] != 0){
              $total += $assignment["grade"];
              $counter++;
            }
          }
          
          if($counter != 0){
            $average = $total / $counter;
          }
          /* ------------------------------------------------------ */
          
          if(empty($assignmentsarray[$classrow["class"] . "," . $row["work_type"]])){
            $classinfo .= " <div class='categoryBox'>
                                <div class='contentWindow categoryHeader'>
                                    <p class='categoryTitle'>" . $row["work_type"]  . "</p>
                                    <button class='addAssignment'>+</button>
                                    <div class='categoryStats'>
                                        <p class='categoryWeight'>Weight:" . $row["weight"] . "</p>
                                        <p class='categoryGrade'>" . $average . "</p>
                                    </div>
                                </div>

                                <div class='categoryMenu'>

                                </div>
                            </div>";
          }
          else{
            $classinfo .=  "  <div class='categoryBox'>
                                <div class='contentWindow categoryHeader'>
                                    <p class='categoryTitle'>" . $row["work_type"]  . "</p>
                                    <button class='addAssignment'>+</button>
                                    <div class='categoryStats'>
                                        <p class='categoryWeight'>Weight:" . $row["weight"] . "</p>
                                        <p class='categoryGrade'>" . $average . "</p>
                                    </div>
                                </div>

                                <div class='categoryMenu'>" .
                                   $assignmentsarray[$classrow["class"] . "," . $row["work_type"]] .
                                "</div>
                            </div>";
          }
        }
      }
      $classinfo .= "</div>";
    }
  }
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Academia</title>
		<link rel="stylesheet" type="text/css" href="homepage.css">
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
      
        <div>
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
        </div>

		<div id="mainbox">
			<div id="classHeader">
				<select id="classSelect">
					<option value="Home">Home</option>
					<?php 
                      echo $classes; 
                    ?>
				</select>
			</div>





			<div class="classContent" id="Home">

				<!-- UPCOMING ASSIGNMENTS -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader">
						<p class="categoryTitle">Upcoming Assignments</p>
					</div>

					<div class="categoryMenu">
						<div class="assignment">
							<div class="assignmentIndicator aiLate"></div>

							<p class="assignmentTitle">Assignment 1</p>
							<p class="dueDateNotify">due:<span>2018-10-24</span></p>
						</div>

						<div class="assignment">
							<div class="assignmentIndicator aiWarning"></div>
							
							<p class="assignmentTitle">Assignment 2</p>
							<p class="dueDateNotify">due:<span>2018-10-24</span></p>
						</div>

						<div class="assignment">
							<div class="assignmentIndicator aiWarning"></div>
							
							<p class="assignmentTitle">Assignment 3</p>
							<p class="dueDateNotify">due:<span>2018-10-24</span></p>
						</div>

						<div class="assignment">
							<div class="assignmentIndicator aiWarning"></div>
							
							<p class="assignmentTitle">Assignment 4</p>
							<p class="dueDateNotify">due:<span>2018-10-24</span></p>
						</div>
					</div>
				</div>

				<!-- GRADES -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader">
						<p class="categoryTitle">Grades</p>
					</div>

					<div class="categoryMenu">
						<div class="assignment">
							<div class="gradeIndicator gradeA"></div>

							<p class="assignmentTitle">Math</p>
							<p class="classGrade">102</p>
						</div>

						<div class="assignment">
							<div class="gradeIndicator gradeB"></div>
							
							<p class="assignmentTitle">Reading</p>
							<p class="classGrade">98</p>
						</div>
					</div>
				</div>
				
				<!-- NEW CLASS -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader" id="addClass">
						<p class="categoryTitle">Add New Class</p>
					</div>

					<div class="categoryMenu">
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
                                  <input class="uiField" name="worktype[]" type="text" placeholder="Category, e.g. Homework" />
                                  <input class="uiField" name="gradeweight[]" type="number" placeholder="Percent of total grade" />
                                  <div class="deleteCategory">X</div>
                              </div>
                          </div>
                          <input id="createClass" type="submit" name="createClass" value="Add Class" />
						</form>
					</div>
				</div>

				<!-- CALCULATOR -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader" id="calculator">
						<p class="categoryTitle">Grade Calculator</p>
					</div>

					<div class="categoryMenu">
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
            <?php
              echo $classinfo;
            ?>
          </div>



		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="homepage.js"></script>
	</body>
</html>