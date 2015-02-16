<?php 
/*
A script used to log the user out, the session is destroyed and session variables are lost.
Written by Prakash.
*/
	session_start();
	$_SESSION = array();
	session_destroy();
	header("Location: ../login.html");
	//redirect to login page
?>