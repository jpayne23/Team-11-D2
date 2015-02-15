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
	
	//load variables
	session_start();
	$deptCode = $_SESSION['deptCode'];	
	$modCode = $_REQUEST['modCode'];
	$modName = $_REQUEST['modName'];
	$modSize = $_REQUEST['modSize'];
	
	$sql = "SELECT ModCode FROM Module WHERE ModCode = '$modCode'";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}	
	
	if($res->numRows() > 0)
	{
		echo "Nope";
	}
	else
	{
		$sql = "INSERT INTO Module(ModCode,Title,Part,Students,Hours,DeptCode) VALUES ($modCode,$modName,substr($modCode,4,1),$modSize,3,$deptCode)";
		
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage().'ins');
		}
	}
?>