<?php

	/* 
	This script is for posting any new module the user wants to create to the
	database so it is available for selection as part of a request.
	
	Implemented by Joe
	
	*/
	
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
	
	//Check that module doesn't already exist
	$sql = "SELECT ModCode FROM Module WHERE ModCode = '$modCode'";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}	
	//When module does exist
	if($res->numRows() > 0)
	{
		echo "Nope";
	}
	else
	{
		//Substring to get part from modCode
		$part = substr($modCode,4,1);
		$sql = "INSERT INTO Module(ModCode,Title,Part,Students,Hours,DeptCode) VALUES ('".$modCode."','".$modName."','".$part."','".$modSize."','3','".$deptCode."');";
		echo $sql;
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage().' inserting the module');
		}
	}
?>