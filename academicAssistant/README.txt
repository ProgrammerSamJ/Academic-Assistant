##Acadamia

Welcome to our website, Academia!
Our website's purpose to allow students to keep track of their academic work, such as grades and assignment due dates, while utilizing a grade calculator that would allow them to keep track of future grades that they would need to obtain in order to achieve a certain grade in the class.

********************************************************************

Our website has two main parts:

  - A combined login/registration page (created through HTML, CSS, and JavaScript, and jQuery)
  - A homepage where all the action takes place, such our class page   selection and organization, adding classes feature, and our grade calculator (created through HTML, CSS, JavaScript, jQuery, PHP, and SQL)

********************************************************************
For the index file, which is our login/registration page, the user can switch between being able to log in or register by clicking the link at the bottom that would either say:

  - "Don't have an account? Register Now!" ---- if you are on the login page.
  - "Already have an account? Log In!" ---- if you are on the registration page.

We added a slide toggle feature to transition between the login and registration page so that there is only one page that will provide the user with the ability to register or login.

For the registration page, there are three features that we keep track of to allow the user to register:

  - All the fields are filled in
  - The password and the re-enter password fields match
  - There isn't an already existing username and password combination that already exists in the database

  ~~~~~~~ In the future, we would like to implement a few more features to improve on our registration page, which would be to authenticate the email, set a minimum for the length of the username and password, and salt the password for the user. ~~~~~~~

Once the user has created an account, the account will be saved in the database, and they can switch back to the login page by clicking the link at the bottom again, which would bring them to the login page. The login page has one feature that allow the user to successfully login:

  - All the fields are must be filled in

Once the user successfully logs in, he will be redirected to our homepage with their own personalized information.

********************************************************************

For our homepage file, there exists a simple home tab for all new users. As the user adds classes onto their account, they would have more tabs that correspond to the classes that they are taking.

~~~~~~~ Note that there is minimal HTML markup on the homepage. The HTML only serves as a container to display the information that will be retrieved and echoed from the database. ~~~~~~~

On the home page, there is a section for upcoming assignments and overall grades of all the classes that the user is currently taking. The criteria for the upcoming assignments section would be that the assignment has to be due within a week's time in the future. The overall grade uses the grades recorded in the database for each class and calculates the grades in each assignment category. Then, we would use the weights, multiple by the corresponding assignment category and then find the final grade for that class. The other two features would be the "Add New Class" and the "Grade Calculator."

The "Add New Class" works exactly as it's labeled ---- it allows the user to add a new class onto their current existing list of classes, or their very first class should they be a new user. The user would have to input a class name, such as Algebra and Biology and all the assignment categories associated with that class, such as Homeworks, Tests, Quizzes, etc, along with the specific weights that these assignment categories have in the grading of the class. For example, normally tests would cover about a 40% impact of the overall class grade. In order to add a new class, the user has to meet 3 requirements:

  - All the fields must be filled in (even for multiple assignment categories)
  - The total weights of the categories must be equal to 100% in order for the class to be added.
  - Do not add a class that has been previously added (an error message will pop up)

The "Grade Calculator" is actually quite simple. All the user needs to do is pick a class, an assignment category in the class, all of the total assignments in that category, and the desired class grade. In the background, there will be calculations that uses the user's current accumulated grade in all categories from the databases and help them calculate the amount of points that the user needs to obtain on future assignments in the specific category to achieve a certain grade in the class.

The final feature, and the most dynamic part of the website, is the addition of a class. The moment a class is added, the drop down menu would create a new option for that class, and also creates a tab for that class. If there are no assignments recorded yet for the users, the assignment categories would only display the headers with their weight and a grade of zero. Once they click on the plus button at the far right of each assignment category, they would be able to add an assignment to the category, and a possible due date or grade.

  ~~~~~~~ Sadly, the user can currently only add one assignment at a time, and in the future we would like to implement a feature where the user can add as many classes as they want. ~~~~~~~

Indicators will appear for the assignment based on the due date and the possible grade that is inputted. If the due date is in the past and there is a grade, the assignment would be completed and have a green, completed, assignment indicator. If the assignment is in the future, and the due date is within a week, there would be a warning indicator with an exclamation mark. If the assignment is in the future and the due date is greater than a week, then the indicator would just be a blue, distant, assignment indicator. If the grade is in the past and the grade is a 0, then there will be a red late indicator for the assignment.

With any assignment inputted, the user would be able to change the due date or the grade for the assignment, and the changes will be updated in the database.

  ~~~~~~~ Unfortunately, there is no way currently to delete the assignment, and that is an implementation that can be added in the future. ~~~~~~~


As previously stated, everything on the webpage is dynamically retrieved and manipulated from the PHP to ensure that the moment a user enters information, the database is updated to reflect that change. If there was any sort of hardcoded HTML, the website would not be as dynamic, and it would not be as appealing as it is now.

The only thing that really concerns us is the risk of SQL injections, and this risk needs to be fixed in the future because the database can be unwantingly accessed by hackers.
