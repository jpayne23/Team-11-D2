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
	
	$sql = "SELECT distinct `Facility` FROM `Facility`;";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$i = 0;
	//$ar = $res->fetchAll();
	//echo json_encode( $ar );
	$ar = array();	
	while ($row = $res->fetchRow())
	{
		//echo '<input type="checkbox" id="c'.$i.'" name="'.$row["facility"].'" value="'.$row["facility"].'">'.$row["facility"].'</input></br>';
		$ar[sizeof($ar)] = $row["facility"];
		$i++;
	}
	echo json_encode($ar);
?>