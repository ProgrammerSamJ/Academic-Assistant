// Resize main tab view with window
$(document).ready(function() {
  
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});

$(window).resize(function() {
	var width = $(window).width();
	$("#mainbox").css("width", width-200 + "px");
	$(".categoryBox").css("width", $("#mainbox").width()-40 + "px");
	$(".categoryMenu").css("width", $("#mainbox").width()-55 + "px");
});


$("#classSelect").change(function() {
    $(".classContent").css("display", "none");

    var selectedClass = "#" + $(this).val();

    $(selectedClass).css("display", "block");
});




$(".categoryHeader").click(function() {
	$(this).next().slideToggle();
}).children(".addAssignment").click(function() {
    return false;
});

var categoryForm = `<div class="newCategory">
                      <input class="addClassField" type="text" name="worktype[]" placeholder="Category, e.g. Homework" />
                      <input class="addClassField" type="number" name="gradeweight[]" placeholder="Percent of total grade" />
                      <div class="deleteCategory">X</div>
                    </div>`;

$("#addCategoryButton").click(function() {
    $("#addClassCategories").append(categoryForm);
});

$("#addClassCategories").on("click", ".deleteCategory", function() {
    $(this).parent().remove();
});

$(".editButton").click(function() {
  
    var classname = "<input type='hidden' name='assignmentClass[]' value='" + $(this).parent().parents(".classContent").attr("id") + "'/>";
    var worktype = "<input type='hidden' name='assignmentWorktype[]' value='" + $(this).parent().parents("div.categoryBox").children(".contentWindow.categoryHeader").children(".categoryTitle").text() + "'/>";
  
    var deleteButton = `<button class="addAssignment deleteAssignment">x</button>`;
    $(this).parent().siblings(".assignmentIndicator").replaceWith(deleteButton);

    var thisTitle = $(this).siblings(".assignmentTitle").text();
    var titleField = `<input class="edithiddenstatus" type="hidden" name="editstatus" value="add"/><input type="text" name="assignmentName[]" placeholder="Assignment Name" 
                     class="editAssignmentTitle" value="` + thisTitle + `" />`;
    $(this).siblings(".assignmentTitle").replaceWith(titleField);
  
    console.log($(this).parent().parent().html());
    console.log($(this).siblings());

    var thisDate = $(this).siblings(".assignmentInfo").find(".dueDate").find("span").text();
    var dateField = `<input type="date" name="due_date[]" class="editDueDate" 
                    value="` + thisDate + `" />`;
    $(this).siblings(".assignmentInfo").find(".dueDate").find("span").replaceWith(dateField);

    var thisGrade = $(this).siblings(".assignmentInfo").find(".assignmentGrade").text();
    var gradeField = `<input type="number" name="assignmentgrade[]" placeholder="Grade" 
                     class="editAssignmentGrade" value="` + thisGrade + `" />`;
    $(this).siblings(".assignmentInfo").find(".assignmentGrade").replaceWith(gradeField);

    $(this).parent().wrapAll("<form />");

    var submitButton = `<input class="addAssignment confirmEdit" name="nowaddAssignment" type="submit" value=">" />`;
    $(this).replaceWith(submitButton);
});

$(".addAssignment").click(function() {
    var classname = "<input type='hidden' name='assignmentClass[]' value='" + $(this).parent().parents(".classContent").attr("id") + "'/>";
    var worktype = "<input type='hidden' name='assignmentWorktype[]' value='" + $(this).parent().parent().children(".contentWindow.categoryHeader").children(".categoryTitle").text() + "'/>";
  
    var newAssignment = `<div class="assignment">
                          <button class="addAssignment deleteAssignment">x</button>
                          <form id="assignmentadder" method="post" action:"homepage.php">
                            <input class="edithiddenstatus" type="hidden" name="editstatus" value="add"/>` + 
                            classname + worktype +
                            `<input type="text" name="assignmentName[]" placeholder="Assignment Name" class="editAssignmentTitle" />
                            <input class="addAssignment confirmEdit" name="nowaddAssignment" type="submit" value=">" />
                            <div class="assignmentInfo">
                              <p class="dueDate">due:
                                <input name="due_date[]" type="date" class="editDueDate" />
                              </p>
                              <input name="assignmentgrade[]" type="number" placeholder="Grade" class="editAssignmentGrade" />
                            </div>
                          </form>
                        </div>`;

    $(this).parent().siblings(".categoryMenu").prepend(newAssignment);
});

$(document).on("click", ".deleteAssignment", function() {
    $(this).parent().remove();
});