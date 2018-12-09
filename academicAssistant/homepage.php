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
    
    /* Check for adding a duplicate class */
    $duplicateClass = $conn->query("SELECT class FROM classes WHERE userid='" . $_SESSION["userid"] . "' AND class='" . $_POST["classname"] . "'");
    $getDup = $duplicateClass->fetch();
    
    $classexists = $getDup["class"];
    
    /* Don't add class if it already exists */
    if($classexists){
      $addclasserrors = "Class already exists!";
    }
    
    /* Create the class that does not exist */
    else{
      $checkempty = 0;
      $totalweight = 0;

      /* Check for empty class information fields and the total weight to be equal to 100% */
      for($x=0; $x<sizeof($_POST["worktype"]); $x++){
        $totalweight += $_POST["gradeweight"][$x];
        if($_POST["classname"] == "" || $_POST["worktype"][$x] == "" || $_POST["gradeweight"][$x] == ""){
          $checkempty = 1;
        }
      }
      
      /* If all fields are filled in and the total weight equal 100% */
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

        /* Addinging the class and work-types into the database */
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
    $checkempty = 0;
    
    /* Check for empty fields */
    if($_POST["assignmentName"] == "" || $_POST["due_date"] == ""){
      $checkempty = 1;
    }
    
    if($checkempty == 0){
      
      /* Allow the editing of classes after clicking the "edit" button */
      if($_POST["editstatus"] == "edit"){
        $deleteassignment = $conn->prepare("DELETE FROM assignments WHERE userid=? AND class=? AND work_type=? AND assignment=?");
        $deleteassignment->execute(array($_SESSION["userid"], $_POST["assignmentClass"], $_POST["assignmentWorktype"], $_POST["assignmentName"]));
      }
      
      /* If the field is blank for the class, the grade is initialized to 0 */
      if($_POST["assignmentgrade"] == ""){
        $_POST["assignmentgrade"] = 0;
      }
      
      /* Convert the date into the Y-m-d format */
      $_POST["due_date"] = date("Y-m-d", strtotime($_POST["due_date"]));

      /* Adding the assignments into the databse */
      $addAssignment = $conn->prepare("INSERT INTO assignments (userid, class, work_type, assignment, due_date, grade) VALUES (:userid, :class, :work_type, :assignment, :due_date, :grade)");
      $insert = $addAssignment->execute(array(":userid" => $userid,
                                              ":class" => $_POST["assignmentClass"],
                                              ":work_type" => $_POST["assignmentWorktype"],
                                              ":assignment" => $_POST["assignmentName"],
                                              ":due_date" => $_POST["due_date"],
                                              ":grade" => $_POST["assignmentgrade"]));
      if($insert === TRUE){
        $insertedclass .= "Successfully inserted assignment(s)!";
      }
      else{
        $addclasserrors .= "There was an error inserting assignment(s)!";
      }
    }
    else{
      $addclasserrors .= "Please fill in all the fields before adding assignments!";
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
        
        /* If the assignment does not exist, create initialization markup in HTML */
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
          if($row["grade"] != 0){
            $indicator = "<div class='assignmentIndicator aiComplete'></div>";
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
        
        /* If the assignment does exist, add onto the already created html markup */
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
          if($row["grade"] != 0){
            $indicator = "<div class='assignmentIndicator aiComplete'></div>";
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


  /* Grabbing the classes, categories, the grade for the user */
  $grabClasses = $conn->query("SELECT class, grade FROM classes WHERE userid ='" . $userid . "'");
  $get = $grabClasses->fetchAll();

  $classes = "";

  /* Add class options onto the drop down fields */
  foreach($get as $row){
    $classes .= "<option value='" . $row["class"] . "'>" . $row["class"] . "</option>";
  }

  /* Grabbing the assignment categories from databse */
  $grabCategories = $conn->query("SELECT work_type FROM workgrade WHERE userid ='" . $userid . "'");
  $get = $grabCategories->fetchAll();

  $categories = "";

  /* Do not duplicate the assignment categories */
  foreach($get as $row){
    if(strpos($categories, $row["work_type"]) === false){
      $categories .= "<option value='" . $row["work_type"] . "'>" . $row["work_type"] . "</option>";
    }
  }

  /* Grabbing all of the classwork of classes for the user */
  $whichClass = $conn->query("SELECT class FROM classes WHERE userid='" . $_SESSION["userid"] . "'");
  $found = $whichClass->fetchAll();

  $classinfo = "";

  /* Creating separate class sections that will appear when you select them from the drop down fields */
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
          
          $updateworkgrade = $conn->prepare("UPDATE workgrade SET grade=? WHERE userid=? AND class=? AND work_type=?");
          $updateworkgrade->execute(array($average, $_SESSION["userid"], $classrow["class"], $row["work_type"]));
          /* ------------------------------------------------------ */
          
          /* Creating an empty class without assignments or grades */
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
          
          /* Add on assignments and grades */
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

  /* Grabbing the overall grade for the class */
  $overallgrade = $conn->query("SELECT class FROM classes WHERE userid='". $_SESSION["userid"] ."'");
  $get = $overallgrade->fetchAll();

  $gradeinfo = "";
  $lettergrade = ""; 
  
  if($get){
    
    /* Calculate the grades, check the grade range to use the correct grade indicator, such as A for grades >= 90 */
    foreach($get as $row){
      $bringingGrades = $conn->query("SELECT weight, grade FROM workgrade WHERE userid='" .$_SESSION["userid"]. "' AND class='" .$row["class"]. "'");
      $bring = $bringingGrades->fetchAll();

      $finalgrade = 0;

      foreach($bring as $rowtwo){
        $finalgrade += ($rowtwo["weight"]/100) * $rowtwo["grade"];
      }

      $updateGrade = $conn->prepare("UPDATE classes SET grade=? WHERE userid=? AND class=?");
      $updateGrade->execute(array($finalgrade, $_SESSION["userid"], $row["class"]));

      if($finalgrade >= 90){
        $lettergrade = "<div class='gradeIndicator gradeA'></div>";
      }
      if($finalgrade >= 80 && $finalgrade <= 89){
        $lettergrade = "<div class='gradeIndicator gradeB'></div>";
      }
      if($finalgrade >= 70 && $finalgrade <= 79){
        $lettergrade = "<div class='gradeIndicator gradeC'></div>";
      }
      if($finalgrade >= 60 && $finalgrade <= 69){
        $lettergrade = "<div class='gradeIndicator gradeD'></div>";
      }
      if($finalgrade < 60){
        $lettergrade = "<div class='gradeIndicator gradeF'></div>";
      }


      $gradeinfo .= "<div class='assignment'>" .
                          $lettergrade .
                        "<p class='assignmentTitle'>" .$row["class"]. "</p>
                        <p class='classGrade'>". $finalgrade ."</p>
                    </div>";

    }
  }
  else{
      $gradeinfo .= "<div class='assignment'>" .
                        "<p class='assignmentTitle'>No grades recorded ..........</p>
                    </div>";
  }

  /* Grabs the upcoming assignments from all of the classes that the user is taking */
  $upcomingassignments = $conn->query("SELECT class, assignment, due_date FROM assignments WHERE userid='" .$_SESSION["userid"]. "'");
  $get = $upcomingassignments->fetchAll();

  $urgency = "";
  $counter = 0;

  if($get){
    foreach($get as $row){
      
      $currenttime = date("Y-m-d");
      
      if($currenttime <= $row["due_date"]){
        $diff = abs(strtotime($row["due_date"]) - strtotime($currenttime));
        $diff = abs($diff / (60*60*24));
        if($diff <= 7){
          $urgency .= "<div class='assignment'>
                        <div class='assignmentIndicator aiWarning'></div>
                        <p class='assignmentTitle'>" .$row["class"] . ": " .$row["assignment"]. "</p>
                        <p class='dueDateNotify'>due:<span>" .$row["due_date"]. "</span></p>
                    </div>";
          $counter++;
        }
        else{
          continue;
        }     
      }
      else{
        continue;
      }
    }
  }
  else{
    $urgency .= "<div class='assignment'>
                    <p class='assignmentTitle'>No assignments...</p>
                </div>";
  }
  
  if($counter == 0){
    $urgency .= "<div class='assignment'>
                    <p class='assignmentTitle'>No assignments...</p>
                </div>";
  }

  /* Grade Calculator */
  $calculated = "";
  if(isset($_POST["gradecalculator"])){
    
    $otherContributions = 0;
    $currentselectedWeight = 0;
    $currentselectedSum = 0;
    
    $getClassInfo = $conn->query("SELECT work_type, weight, grade FROM workgrade WHERE userid='" .$_SESSION["userid"]. "' AND class='" .$_POST["calculatorClass"]. "'");
    $get = $getClassInfo->fetchAll();
    
    $getSum = $conn->query("SELECT grade FROM assignments WHERE userid='" . $_SESSION["userid"]."' AND class='" .$_POST["calculatorClass"]. "' AND work_type='" .$_POST["calculatorCategory"]. "'");
    $numberofgrades = $getSum->fetchAll();
    
    $getWeight = $conn->query("SELECT weight FROM workgrade WHERE userid='" . $_SESSION["userid"]."' AND class='" .$_POST["calculatorClass"]. "' AND work_type='" .$_POST["calculatorCategory"]. "'");
    $weight = $getWeight->fetch();
    
    foreach($get as $row){
      if($row["work_type"] == $_POST["calculatorCategory"]){
        continue;
      }
      else{
        $otherContributions += ($row["weight"] / 100) * $row["grade"];
      }
    }
    
    foreach($numberofgrades as $gotrow){
      $currentselectedSum += $gotrow["grade"];
    }
    
    $currentselectedWeight = $weight["weight"];
    
    $_POST["desiredgrade"];
    $_POST["totalassignments"];
    
    /* Final algorithm to calculate the needed points to achieve the indicated grade */
    $calculatedSum = ((($_POST["desiredgrade"] - $otherContributions) / ($currentselectedWeight/100)) * $_POST["totalassignments"]) - $currentselectedSum;
    
    $calculated = "You need " .$calculatedSum. " points between your remaining assignments.";

  }
  
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Academia</title>
		<link rel="stylesheet" type="text/css" href="homepage.css">
	</head>

	<body>
      
        <!-- Header bar -->
		<div id="header">
			<span id="logo">ACADEMIA</span>

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
      
        <!-- Area for the error/non-error information -->
        <div id="echoMessage">
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

        <!-- Field for the Home/Classes drop down field -->
		<div id="mainbox">
			<div id="classHeader">
				<select id="classSelect">
					<option value="Home">Home</option>
					    <?php 
                echo $classes; 
              ?>
				</select>
			</div>

            <!-- Initial class homepage -->
			<div class="classContent" id="Home">

				<!-- Upcoming Assignments -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader">
						<p class="categoryTitle">Upcoming Assignments</p>
					</div>

					<div class="categoryMenu">
                <?php 
                  echo $urgency;
                ?>
					</div>
				</div>

				<!-- Grades -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader">
						<p class="categoryTitle">Grades</p>
					</div>

					<div class="categoryMenu">
                <?php
                  echo $gradeinfo;
                ?>
					</div>
				</div>
				
				<!-- Adding on a new class -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader" id="addClass">
						<p class="categoryTitle">Add New Class</p>
					</div>

					<div class="categoryMenu">
						<form id="addClass" method="post" action="homepage.php">
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
              <input id="createClass" class="homeButton" type="submit" name="createClass" value="Add Class" />
						</form>
					</div>
				</div>

				<!-- Calculator -->
				<div class="categoryBox">
					<div class="contentWindow categoryHeader" id="calculator">
						<p class="categoryTitle">Grade Calculator</p>
					</div>

					<div class="categoryMenu">
						<form id="calculateGradeWanted" method="post" action="homepage.php">

              <div class="calculatorItem">
                  <p class="interfaceLabel">Class:</p>
                  <select id="calculatorClassSelector" name="calculatorClass">
                      <?php
                        echo $classes
                      ?>
                  </select>
              </div>

              <div class="calculatorItem">
                  <p class="interfaceLabel">Category:</p>
                  <select id="calculatorCategory" name="calculatorCategory">
                      <?php
                        echo $categories;
                      ?>
                  </select>
              </div>

              <div class="calculatorItem">
                  <p class="interfaceLabel">Total Assignments in Category:</p>
                  <input type="text" name="totalassignments" placeholder="0" class="uiField" />
              </div>

              <div class="calculatorItem">
                  <p class="interfaceLabel">Desired Class Grade:</p>
                  <input type="text" name="desiredgrade" placeholder="0" class="uiField" />
              </div>

                <input id="calculate" class="homeButton" type="submit" name="gradecalculator" value="Calculate" />

						</form>
                        
            <p id="result">
              <?php
                echo $calculated;
              ?>
            </p>
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