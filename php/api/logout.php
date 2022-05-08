<?php
session_start(); // get information about the session
require_once("../../constants.php"); // constants.php is here mandatory for constants used for logging

//test
file_log("logout", __FILE__, $_SESSION['username'], "");

session_destroy(); // destroying session

header("location: ../login.php"); // the user is not authenticated anymore and, therefore, it is redirected to the Login page
exit;
?>