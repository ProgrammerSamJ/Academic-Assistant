function openTab(thisCourse, courseName){
  var courseTabs, courseInfo;
  courseInfo = document.getElementsbyClassName("courseInfo");
  for(var i=0; i<courseInfo.length; i++){
    courseInfo[i].style.display = "none";
  }
  courseTabs = document.getElementsbyClassName("courseTabs");
  for(var i=0; i<courseTabs.length; i++){
    courseTabs[i].className = courseTabs[i].className.replace(" active", "");
  }
  document.getElementbyId(courseName).style.display = "block";
  thisCourse.currentTarget.className += " active";
}