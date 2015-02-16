<?php
	/*
	A php script that will check what the highest round of all requests in the database
	is and return it to the user. It is run on load of the website and does not require any input.
	Written by Prakash.
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
	
	$sql = "SELECT MAX(RoundID) as roundid FROM Request;";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	while ($row = $res->fetchRow())
	{
		echo $row["roundid"];
	}

?>