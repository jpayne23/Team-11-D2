<?php
/*
A php script to retrieve the number of students on each module. Used 
in the room selector to set the capacity of multiroom selections. 
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
	
	$modCode = $_REQUEST['modCode'];
	$sql = "SELECT distinct Students FROM Module WHERE ModCode = '".$modCode."';";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}//query the database
	while ($row = $res->fetchRow())
	{
		echo $row["students"];
	}//send back results
?>