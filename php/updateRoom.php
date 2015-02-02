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
	
	$building = $_REQUEST['building'];
	$sql = "SELECT room FROM `Room` WHERE buildingcode = '".$building."';";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
		
	echo '<select name="room" id="room1">';
	echo '<option>Any</option>';
	if ($building != 'Any')
	{	
		while ($row = $res->fetchRow())
		{
			echo '<option>'.$row["room"].'</option>';
		}
	}
	else 
	{
		echo '<option>Any</option>';
	}	
	echo '</select>';
?>