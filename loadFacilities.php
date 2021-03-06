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
	
	$sql = "SELECT distinct `Facility` FROM `Facilities`;";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$i = 0;
	while ($row = $res->fetchRow())
	{
		//remove all whitespaces so we can use it as an ID.
		$id = $string = preg_replace('/\s+/', '', $row["facility"]);
		
		//print out a checkbox for each facility
		echo '<input type="checkbox" id='.$id.' name="'.$row["facility"].'" value="'.$row["facility"].'">'.$row["facility"].'</input></br>';
		$i++;
	}
?>