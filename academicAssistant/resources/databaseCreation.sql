--
-- Creating the database for the user accounts and the academic information that they input
--
--
-- Creating the initial database
CREATE DATABASE IF NOT EXISTS academia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Creating the table for the registered accounts
CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(255) AUTO_INCREMENT,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar (320) NOT NULL,
  PRIMARY KEY (`userid`)
);

--
-- Creating the table for grades associated with each user's class
CREATE TABLE IF NOT EXISTS `classes` (
  `userid` int references users(userid),
  `class` varchar(100) references classes(class),
  `grade` int(3) DEFAULT 0
);

--
-- Creating a table for the classes that each user is currently taking
CREATE TABLE IF NOT EXISTS `workgrade` (
  `userid` int references users(userid),
  `class` varchar(100) NOT NULL,
  `work_type` varchar(100) NOT NULL,
  `weight` int(3) NOT NULL,
  `grade` int(3) DEFAULT 0
);

--
-- Creating the table for assignments associated with each user's class
CREATE TABLE IF NOT EXISTS `assignments` (
  `userid` int references users(userid),
  `class` varchar(100) references classes(class),
  `work_type` varchar(100) references classes(work_type),
  `assignment` varchar(100) NOT NULL,
  `due_date` date NOT NULL,
  `grade` int(3) DEFAULT 0
); 
