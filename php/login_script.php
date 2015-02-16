<?php 
/* 
This script is used to query the database using the login credentials provided by the user. 
It will generate an error page if the credentials are not recognised and will pass the deptCode
of the user and whether or not the page was being viewed in accessibility mode or not onto the main
system.
Written by Prakash.
*/
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	//connect to the database
	$username1 = $_POST["username"];
	$password1 = $_POST["password"];
	$access = $_POST["access"];
	//retrieve information from the login page
	$username1 = stripslashes($username1);
	$password1 = stripslashes($password1);
	$username1 = mysql_real_escape_string($username1);
	$password1 = mysql_real_escape_string($password1);
	//to protect against MySQL Injection, a security measure
	$sql = "SELECT `DeptCode` FROM `Users` WHERE `UserName` = '".$username1."' and `Password` = '".$password1."' ";			
	$res =& $db->query($sql);
	//query the database
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	if($res -> numRows() == 0)//if credentials not found, redirect to error message
		header("location:../error.html");
	else
	{
		while ($row = $res->fetchRow())
		{//else send deptCode and boolean access to homepage.php
			session_start();
			$_SESSION["deptCode"] = $row["deptcode"];
			if($access == "on")
			{
				$_SESSION["access"] = "yes";
			}
			else
			{
				$_SESSION["access"] = "no";
			}
			header("location:../homepage.php");
		}
	}
?>