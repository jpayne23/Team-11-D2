<?php 
	session_start();
	unset($_SESSION["deptCode"]);
	header("Location: ../login.html")
?>