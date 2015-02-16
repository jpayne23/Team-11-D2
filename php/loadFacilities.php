<?php
	/*
	A php script to get the facilities from the database. 
	It does not need any input and will return an array of facilities.
	Written by Prakash and Bhav.
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
	//build the sql
	$sql = "SELECT distinct `Facility` FROM `Facility`;";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}//run the query
	$ar = array();	
	while ($row = $res->fetchRow())
	{
		$ar[sizeof($ar)] = $row["facility"];
	}
	echo json_encode($ar);
	//send the results array back to the javascript
?>