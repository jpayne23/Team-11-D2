<!--
When the user clicks submit all requests, we set all of the current pending 
requests for the user to be submitted.

Implemented by Jack
-->
<?php
	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	session_start();
	$deptCode = $_SESSION['deptCode'];	
	
	$sql = "UPDATE Request SET Status='Submitted' WHERE Status='Pending' AND DeptCode = '$deptCode'";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
?>