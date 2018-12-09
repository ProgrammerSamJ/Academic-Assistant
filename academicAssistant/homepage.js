// Resize main box  with window
$(document).ready(function() {
  
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});


// Resise main box when window is resized
$(window).resize(function() {
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});

// Hide all classes and display the one corresponding to the selection.
$("#classSelect").change(function() {
    $(".classContent").css("display", "none");

    var selectedClass = "#" + $(this).val();

    $(selectedClass).css("display", "block");
});

// Prevent slideToggle of category content when add assignment button is pressed.
$(".categoryHeader").click(function() {
	$(this).next().slideToggle();
}).children(".addAssignment").click(function() {
    return false;
});

// Add a new category when the button is pressed in "Add Class"
var categoryForm = `<div class="newCategory">
                      <input class="uiField" type="text" name="worktype[]" placeholder="Category, e.g. Homework" />
                      <input class="uiField" type="number" name="gradeweight[]" placeholder="Percent of total grade" />
                      <div class="deleteCategory">X</div>
                    </div>`;

$("#addCategoryButton").click(function() {
    $("#addClassCategories").append(categoryForm);
});

// Remove category when corresponding button is pressed.
$("#addClassCategories").on("click", ".deleteCategory", function() {
    $(this).parent().remove();
});

// When the edit button for an assignment is clicked, collect the information from
// the assignment and replace everything with fields that contain that information.
// Wrap the entire thing in a form so that the information can be collected.
$(".editButton").click(function() {
  
    var classname = "<input type='hidden' name='assignmentClass' value='" + $(this).parent().parents(".classContent").attr("id") + "'/>";
    var worktype = "<input type='hidden' name='assignmentWorktype' value='" + $(this).parent().parents("div.categoryBox").children(".contentWindow.categoryHeader").children(".categoryTitle").text() + "'/>";
  
    var deleteButton = `<button class="addAssignment deleteAssignment">x</button>`;
    $(this).parent().siblings(".assignmentIndicator").replaceWith(deleteButton);

    var thisTitle = $(this).siblings(".assignmentTitle").text();
    var titleField = classname + worktype + `<input class="edithiddenstatus" type="hidden" name="editstatus" value="edit"/><input type="text" name="assignmentName" placeholder="Assignment Name" 
                     class="editAssignmentTitle" value="` + thisTitle + `" />`;
    $(this).siblings(".assignmentTitle").replaceWith(titleField);
  
    console.log($(this).parent().parent().html());
    console.log($(this).siblings());

    var thisDate = $(this).siblings(".assignmentInfo").find(".dueDate").find("span").text();
    var dateField = `<input type="date" name="due_date" class="editDueDate" 
                    value="` + thisDate + `" />`;
    $(this).siblings(".assignmentInfo").find(".dueDate").find("span").replaceWith(dateField);

    var thisGrade = $(this).siblings(".assignmentInfo").find(".assignmentGrade").text();
    var gradeField = `<input type="number" name="assignmentgrade" placeholder="Grade" 
                     class="editAssignmentGrade" value="` + thisGrade + `" />`;
    $(this).siblings(".assignmentInfo").find(".assignmentGrade").replaceWith(gradeField);

    $(this).parent().wrapAll("<form id='ssignmentadder' method='post' action:'homepage.php' />");

    var submitButton = `<input class="addAssignment confirmEdit" name="nowaddAssignment" type="submit" value=">" />`;
    $(this).replaceWith(submitButton);
});

// Add a new assignment field when the add assignment button is clicked.
$(".addAssignment").click(function() {
    var classname = "<input type='hidden' name='assignmentClass' value='" + $(this).parent().parents(".classContent").attr("id") + "'/>";
    var worktype = "<input type='hidden' name='assignmentWorktype' value='" + $(this).parent().parent().children(".contentWindow.categoryHeader").children(".categoryTitle").text() + "'/>";
  
    var newAssignment = `<div class="assignment">
                          <button class="addAssignment deleteAssignment">x</button>
                          <form id="assignmentadder" method="post" action:"homepage.php">
                            <input class="edithiddenstatus" type="hidden" name="editstatus" value="add"/>` + 
                            classname + worktype +
                            `<input type="text" name="assignmentName" placeholder="Assignment Name" class="editAssignmentTitle" />
                            <input class="addAssignment confirmEdit" name="nowaddAssignment" type="submit" value=">" />
                            <div class="assignmentInfo">
                              <p class="dueDate">due:
                                <input name="due_date" type="date" class="editDueDate" />
                              </p>
                              <input name="assignmentgrade" type="number" placeholder="Grade" class="editAssignmentGrade" />
                            </div>
                          </form>
                        </div>`;

    $(this).parent().siblings(".categoryMenu").prepend(newAssignment);
});

// Deletes the corresponding assignment.
$(document).on("click", ".deleteAssignment", function() {
    $(this).parent().remove();
});