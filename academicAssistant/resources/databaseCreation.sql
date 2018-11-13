--
-- Creating the database for the user accounts and the academic information that they input
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Creating the table for the registered accounts
--

CREATE TABLE `users` (
  `userid` int(255) UNSIGNED NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar (320) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `classes` (
  `userid` int(255) UNSIGNED NOT NULL;
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Setting the indices for table `users`
--

ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);
  
--
-- Auto-incrementing the `users` table
--
  
ALTER TABLE `users`
  MODIFY `userid` int(255) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;